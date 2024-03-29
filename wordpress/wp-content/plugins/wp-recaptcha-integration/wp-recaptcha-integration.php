<?php
/*
Plugin Name: WP reCaptcha Integration
Plugin URI: https://wordpress.org/plugins/wp-recaptcha-integration/
Description: Integrate reCaptcha in your blog. Supports no Captcha (new style recaptcha) as well as the old style reCaptcha. Provides of the box integration for signup, login, comment forms, lost password, Ninja Forms and contact form 7.
Version: 1.0.9
Author: Jörn Lund
Author URI: https://github.com/mcguffin/
*/

/*  Copyright 2014  Jörn Lund  (email : joern AT podpirate DOT org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/**
 *	Plugin base Class
 */
class WP_reCaptcha {

	private static $_is_network_activated = null;

	private $_has_api_key = false;

	private $last_error = '';
	private $_last_result;
	
	private $_counter = 0;

	private $_captcha_instance = null;
	
	/**
	 *	Holding the singleton instance
	 */
	private static $_instance = null;

	/**
	 *	@return WP_reCaptcha
	 */
	public static function instance(){
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 *	Prevent from creating more instances
	 */
	private function __clone() { }

	/**
	 *	Prevent from creating more than one instance
	 */
	private function __construct() {
		add_option('recaptcha_flavor','grecaptcha'); // local
		add_option('recaptcha_theme','light'); // local
		add_option('recaptcha_disable_submit',false); // local
		add_option('recaptcha_publickey',''); // 1st global -> then local
		add_option('recaptcha_privatekey',''); // 1st global -> then local
		add_option('recaptcha_language',''); // 1st global -> then local

		if ( WP_reCaptcha::is_network_activated() ) {
			add_site_option('recaptcha_publickey',''); // 1st global -> then local
			add_site_option('recaptcha_privatekey',''); // 1st global -> then local
			
			add_site_option('recaptcha_enable_comments' , true); // global
			add_site_option('recaptcha_enable_signup' , true); // global
			add_site_option('recaptcha_enable_login' , false); // global
			add_site_option('recaptcha_enable_lostpw' , false); // global
			add_site_option('recaptcha_disable_for_known_users' , true); // global
			$this->_has_api_key = get_site_option( 'recaptcha_publickey' ) && get_site_option( 'recaptcha_privatekey' );
		} else {
			add_option('recaptcha_enable_comments' , true); // global
			add_option('recaptcha_enable_signup' , true); // global
			add_option('recaptcha_enable_login' , false); // global
			add_option('recaptcha_enable_lostpw' , false); // global
			add_option('recaptcha_disable_for_known_users' , true); // global
			$this->_has_api_key = get_option( 'recaptcha_publickey' ) && get_option( 'recaptcha_privatekey' );
		}

		if ( $this->_has_api_key ) {

			add_action('init' , array(&$this,'init') , 9 );
			add_action('plugins_loaded' , array(&$this,'plugins_loaded') );

		}

		register_activation_hook( __FILE__ , array( __CLASS__ , 'activate' ) );
		register_deactivation_hook( __FILE__ , array( __CLASS__ , 'deactivate' ) );
		register_uninstall_hook( __FILE__ , array( __CLASS__ , 'uninstall' ) );

	}
	
	/**
	 *	Load ninja/cf7 php files if necessary
	 *	Hooks into 'plugins_loaded'
	 */
	function plugins_loaded() {
		if ( $this->_has_api_key ) {
			// NinjaForms support
			// check if ninja forms is present
			if ( class_exists('Ninja_Forms') || function_exists('ninja_forms_register_field') )
				WP_reCaptcha_NinjaForms::instance();

			// CF7 support
			// check if contact form 7 forms is present
			if ( function_exists('wpcf7') )
				WP_reCaptcha_ContactForm7::instance();

			// WooCommerce support
			// check if woocommerce is present
			if ( function_exists('WC') || class_exists('WooCommerce') )
				WP_reCaptcha_WooCommerce::instance();

		}
	}
	/**
	 *	Init plugin
	 *	set hooks
	 */
	function init() {
		load_plugin_textdomain( 'wp-recaptcha-integration', false , dirname( plugin_basename( __FILE__ ) ).'/languages/' );
		$require_recaptcha = $this->is_required();
		
		if ( $require_recaptcha ) {
			add_action( 'wp_head' , array($this,'recaptcha_head') );
			add_action( 'wp_footer' , array($this,'recaptcha_foot') );
			
			if ( $this->get_option('recaptcha_enable_signup') || $this->get_option('recaptcha_enable_login')  || $this->get_option('recaptcha_enable_lostpw') ) {
				add_action( 'login_head' , array(&$this,'recaptcha_head') );
				add_action( 'login_footer' , array(&$this,'recaptcha_foot') );
			}
			if ( $this->get_option('recaptcha_enable_comments') ) {
				/*
				add_action('comment_form_after_fields',array($this,'print_recaptcha_html'),10,0);
				/*/
				add_filter('comment_form_defaults',array($this,'comment_form_defaults'),10);
				//*/
				add_action('pre_comment_on_post',array($this,'recaptcha_check_or_die'));
			}
			if ( $this->get_option('recaptcha_enable_signup') ) {
				// buddypress suuport.
				if ( function_exists('buddypress') ) {
					add_action('bp_account_details_fields',array($this,'print_recaptcha_html'),10,0);
					add_filter('bp_signup_pre_validate',array(&$this,'recaptcha_check_or_die'),99 );
				} else {
					add_action('register_form',array($this,'print_recaptcha_html'),10,0);
					add_filter('registration_errors',array(&$this,'registration_errors'));
				}
				if ( is_multisite() ) {
					add_action( 'signup_extra_fields' , array($this,'print_recaptcha_html'),10,0);
					add_filter('wpmu_validate_user_signup',array(&$this,'wpmu_validate_user_signup'));
				}
				
			}
			if ( $this->get_option('recaptcha_enable_login') ) {
				add_action('login_form',array(&$this,'print_recaptcha_html'),10,0);
				add_filter('wp_authenticate_user',array(&$this,'deny_login'),99 );
			}
			if ( $this->get_option('recaptcha_enable_lostpw') ) {
				add_action('lostpassword_form' , array($this,'print_recaptcha_html'),10,0);
//*
				add_filter('lostpassword_post' , array(&$this,'recaptcha_check_or_die') , 99 );
/*/ // switch this when pull request accepted and included in official WC release.
				add_filter('allow_password_reset' , array(&$this,'wp_error') );
//*/
			}
			if ( 'WPLANG' === $this->get_option( 'recaptcha_language' ) ) 
				add_filter( 'wp_recaptcha_language' , array( &$this,'recaptcha_wplang' ) );
		}
	}
	
	
	public function captcha_instance() {
		if ( is_null( $this->_captcha_instance ) ) {
			switch ( $this->get_option( 'recaptcha_flavor' ) ) {
				case 'grecaptcha':
					$this->_captcha_instance = WP_reCaptcha_NoCaptcha::instance();
					break;
				case 'recaptcha':
					$this->_captcha_instance = WP_reCaptcha_ReCaptcha::instance();
					break;
			}
		}
		return $this->_captcha_instance;
	}
	
	/**
	 *	returns if recaptcha is required.
	 *
	 *	@return bool
	 */
	function is_required() {
		$is_required = ! ( $this->get_option('recaptcha_disable_for_known_users') && current_user_can( 'read' ) );
		return apply_filters( 'wp_recaptcha_required' , $is_required );
	}
	
	
	
 	
	//////////////////////////////////
	// 	Displaying
	// 

	/**
	 *	print recaptcha stylesheets
	 *	hooks into `wp_head`
	 */
	function recaptcha_head( ) {
		$this->begin_inject( );
		$this->captcha_instance()->print_head();
		$this->end_inject( );
 	}
 	
	/**
	 *	Print recaptcha scripts
	 *	hooks into `wp_footer`
	 *
	 */
	function recaptcha_foot( ) {
		$this->begin_inject( );
		
		// getting submit buttons of an elements form
		if ( $this->get_option( 'recaptcha_disable_submit' ) ) { 
			?><script type="text/javascript">
			function get_form_submits(el){
				var form,current=el,ui,type,slice = Array.prototype.slice,self=this;
				this.submits=[];
				this.form=false;
				
				this.setEnabled=function(e){
					for ( var s in self.submits ) {
						if (e) self.submits[s].removeAttribute('disabled');
						else  self.submits[s].setAttribute('disabled','disabled');
					}
					return this;
				};
				while ( current && current.nodeName != 'BODY' && current.nodeName != 'FORM' ) {
					current = current.parentNode;
				}
				if ( !current || current.nodeName != 'FORM' ) 
					return false;
				this.form=current;
				ui=slice.call(this.form.getElementsByTagName('input')).concat(slice.call(this.form.getElementsByTagName('button')));
				for (var i in ui ) if ( (type=ui[i].getAttribute('TYPE')) && type=='submit' ) this.submits.push(ui[i]);
				return this;
			}
			</script><?php
		}
		$this->captcha_instance()->print_foot();
		
		$this->end_inject( );
	}
	
	/**
	 *	Print recaptcha HTML. Use inside a form.
	 *
	 */
 	function print_recaptcha_html( $attr = array() ) {
		echo $this->begin_inject( );
 		echo $this->recaptcha_html( $attr );
		echo $this->end_inject( );
 	}
 	
	/**
	 *	Get recaptcha HTML.
	 *
	 *	@return string recaptcha html
	 */
 	function recaptcha_html( $attr = array() ) {
		return $this->captcha_instance()->get_html( $attr );
 	}

	/**
	 *	HTML comment with some notes (beginning)
	 *	
	 *	@param $return bool Whether to print or to return the comment
	 *	@param $moretext string Additional information being included in the comment
	 *	@return null|string HTML-Comment
	 */
	function begin_inject($return = false,$moretext='') {
		$html = "\n<!-- BEGIN recaptcha, injected by plugin wp-recaptcha-integration $moretext -->\n";
		if ( $return ) return $html;
		echo $html;
	}
	/**
	 *	HTML comment with some notes (ending)
	 *	
	 *	@param $return bool Whether to print or to return the comment
	 *	@return null|string HTML-Comment
	 */
	function end_inject( $return = false ) {
		$html = "\n<!-- END recaptcha -->\n";
		if ( $return ) return $html;
		echo $html;
	}
	
	/**
	 *	Display recaptcha on comments form.
	 *	filter function for `comment_form_defaults`
	 *
	 *	@see filter doc `comment_form_defaults`
	 */
	function comment_form_defaults( $defaults ) {
		$defaults['comment_notes_after'] .= '<p>' . $this->recaptcha_html() . '</p>';
		return $defaults;
	}

	//////////////////////////////////
	// 	Verification
	// 

	/**
	 *	Get last result of recaptcha check
	 *	@return string recaptcha html
	 */
	function get_last_result() {
		return $this->captcha_instance()->get_last_result();
	}
	
	/**
	 *	Check recaptcha
	 *
	 *	@return bool false if check does not validate
	 */
	function recaptcha_check( ) {
		return $this->captcha_instance()->check();
	}
	
	/**
	 *	check recaptcha on login
	 *	filter function for `wp_authenticate_user`
	 *
	 *	@param $user WP_User
	 *	@return object user or wp_error
	 */
	function deny_login( $user ) {
		if ( isset( $_POST["log"]) )
			$user = $this->wp_error( $user );
		return $user;
	}
	
	/**
	 *	check recaptcha on registration
	 *	filter function for `registration_errors`
	 *
	 *	@param $errors WP_Error
	 *	@return WP_Error with captcha error added if test fails.
	 */
	function registration_errors( $errors ) {
		if ( isset( $_POST["user_login"]) )
			$errors = $this->wp_error_add( $errors );
		return $errors;
	}
	
	/**
	 *	check recaptcha WPMU signup
	 *	filter function for `wpmu_validate_user_signup`
	 *
	 *	@see filter hook `wpmu_validate_user_signup`
	 */
	function wpmu_validate_user_signup( $result ) {
		if ( isset( $_POST['stage'] ) && $_POST['stage'] == 'validate-user-signup' )
			$result['errors'] = $this->wp_error_add( $result['errors'] , 'generic' );
		return $result;
	}
	
	
	/**
	 *	check recaptcha and return WP_Error on failure.
	 *	filter function for `allow_password_reset`
	 *
	 *	@param $param mixed return value of funtion when captcha validates
	 *	@return mixed will return argument $param an success, else WP_Error
	 */
	function wp_error( $param , $error_code = 'captcha_error' ) {
		if ( ! $this->recaptcha_check() ) {
			return new WP_Error( $error_code ,  __("<strong>Error:</strong> the Captcha didn’t verify.",'wp-recaptcha-integration') );
		} else {
			return $param;
		}
	}
	/**
	 *	check recaptcha and return WP_Error on failure.
	 *	filter function for `allow_password_reset`
	 *
	 *	@param $param mixed return value of funtion when captcha validates
	 *	@return mixed will return argument $param an success, else WP_Error
	 */
	function wp_error_add( $param , $error_code = 'captcha_error' ) {
		if ( ! $this->recaptcha_check() ) {
			return new WP_Error( $error_code ,  __("<strong>Error:</strong> the Captcha didn’t verify.",'wp-recaptcha-integration') );
		} else {
			return $param;
		}
	}
	
	/**
	 *	Check recaptcha and wp_die() on fail
	 *	hooks into `pre_comment_on_post`, `lostpassword_post`
	 */
 	function recaptcha_check_or_die( ) {
 		if ( ! $this->recaptcha_check() ) {
 			$err = new WP_Error('comment_err',  __("<strong>Error:</strong> the Captcha didn’t verify.",'wp-recaptcha-integration') );
 			wp_die( $err );
 		}
 	}
 	
	
	//////////////////////////////////
	// 	Options
	// 

	/**
	 *	Get plugin option by name.
	 *	
	 *	@param $option_name string
	 *	@return bool false if check does not validate
	 */
	public function get_option( $option_name ) {
		switch ( $option_name ) {
			case 'recaptcha_publickey': // first try local, then global
			case 'recaptcha_privatekey':
				$option_value = get_option($option_name);
				if ( ! $option_value && WP_reCaptcha::is_network_activated() )	
					$option_value = get_site_option( $option_name );
				return $option_value;
			case 'recaptcha_enable_comments': // global on network. else local
			case 'recaptcha_enable_signup':
			case 'recaptcha_enable_login':
			case 'recaptcha_enable_lostpw':
			case 'recaptcha_disable_for_known_users':
			case 'recaptcha_enable_wc_order':
				if ( WP_reCaptcha::is_network_activated() )
					return get_site_option($option_name);
				return get_option( $option_name );
			default: // always local
				return get_option($option_name);
		}
	}
	
	/**
	 *	@return bool return if google api is configured
	 */
	function has_api_key() {
		return $this->_has_api_key;
	}
	

	//////////////////////////////////
	// 	Activation
	// 

	/**
	 *	Fired on plugin activation
	 */
	public static function activate() {
	}

	/**
	 *	Fired on plugin deactivation
	 */
	public static function deactivate() {
	}
	/**
	 *	Uninstall
	 */
	public static function uninstall() {
		if ( is_multisite() ) {
			delete_site_option( 'recaptcha_publickey' );
			delete_site_option( 'recaptcha_privatekey' );
			delete_site_option( 'recaptcha_enable_comments' );
			delete_site_option( 'recaptcha_enable_signup' );
			delete_site_option( 'recaptcha_enable_login' );
			delete_site_option( 'recaptcha_enable_wc_checkout' );
			delete_site_option( 'recaptcha_disable_for_known_users' );
			
			foreach ( wp_get_sites() as $site) {
				switch_to_blog( $site["blog_id"] );
				delete_option( 'recaptcha_publickey' );
				delete_option( 'recaptcha_privatekey' );
				delete_option( 'recaptcha_flavor' );
				delete_option( 'recaptcha_theme' );
				delete_option( 'recaptcha_language' );
				restore_current_blog();
			}
		} else {
			delete_option( 'recaptcha_publickey' );
			delete_option( 'recaptcha_privatekey' );
		
			delete_option( 'recaptcha_flavor' );
			delete_option( 'recaptcha_theme' );
			delete_option( 'recaptcha_language' );
			delete_option( 'recaptcha_enable_comments' );
			delete_option( 'recaptcha_enable_signup' );
			delete_option( 'recaptcha_enable_login' );
			delete_option( 'recaptcha_enable_wc_checkout' );
			delete_option( 'recaptcha_disable_for_known_users' );
		}
	}
	
	/**
	 *	Get plugin option by name.
	 *	
	 *	@return bool true if plugin is activated on network
	 */
	static function is_network_activated() {
		if ( is_null(self::$_is_network_activated) ) {
			if ( ! is_multisite() )
				return false;
			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

			self::$_is_network_activated = is_plugin_active_for_network( basename(dirname(__FILE__)).'/'.basename(__FILE__) );
		}
		return self::$_is_network_activated;
	}
	
	
	

	//////////////////////////////////
	// 	Language
	// 
	
	/**
	 *	Rewrite WP get_locale() to recaptcha lang param.
	 *
	 *	@return string recaptcha language
	 */
	function recaptcha_wplang( ) {
		$locale = get_locale();
		/* Sometimes WP uses different locales the the ones supported by nocaptcha. */
		$mapping = array(
			'es_MX' => 'es-419',
			'es_PE' => 'es-419',
			'es_CL' => 'es-419',
			'he_IL' => 'iw',
		);
		if ( isset( $mapping[$locale] ) )
			$locale = $mapping[$locale];
		return $this->recaptcha_language( $locale );
	}
	/**
	 *	Rewrite WP get_locale() to recaptcha lang param.
	 *
	 *	@return string recaptcha language
	 */
	function recaptcha_language( $lang ) {
		$lang = str_replace( '_' , '-' , $lang );
		
		$langs = $this->get_supported_languages();
		// direct hit: return it.
		if ( isset($langs[$lang]) )
			return $lang;
		
		// remove countrycode
		$lang = preg_replace('/-(.*)$/','',$lang);
		if ( isset($langs[$lang]) )
			return $lang;
		
		// lang does not exist.
		return '';
	}

	/**
	 *	Get languages supported by current recaptcha flavor.
	 *
	 *	@return array languages supported by recaptcha.
	 */
	function get_supported_languages( ) {
		return $this->captcha_instance()->get_supported_languages();
	}
	
}

/**
 * Autoload WPAA Classes
 *
 * @param string $classname
 */
function wp_recaptcha_integration_autoload( $classname ) {
	$class_path = dirname(__FILE__). sprintf('/inc/class-%s.php' , strtolower( $classname ) ) ; 
	if ( file_exists($class_path) )
		require_once $class_path;
}
spl_autoload_register( 'wp_recaptcha_integration_autoload' );



WP_reCaptcha::instance();


if ( is_admin() )
	WP_reCaptcha_Options::instance();
