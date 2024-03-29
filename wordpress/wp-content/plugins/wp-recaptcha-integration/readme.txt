=== WordPress ReCaptcha Integration ===
Contributors: podpirate
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8NKC6TCASUXE
Tags: security, captcha, recaptcha, no captcha, login, signup, contact form 7, ninja forms
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

reCaptcha for login, signup, comment forms, Ninja Forms and Contact Form 7.

== Description ==

Integrate reCaptcha in your blog. Supports no Captcha as well as old style recaptcha. 
Provides of the box integration for signup, login, comment forms, Ninja Forms and contact 
form 7 as well as a plugin API for your own integrations.

= Features: =
- Secures login, signup and comments with a recaptcha.
- Supports old as well as new reCaptcha.
- Multisite Support
- BuddyPress Support
- WooCommerce Support (Only checkout, registration and login form. Not password reset)
- [Ninja Forms](http://ninjaforms.com/) integration
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) integration

= Localizations =
- Brazilian Portuguese (thanks to [Vinícius Ferraz](http://www.viniciusferraz.com))
- Spanish (thanks to [Ivan Yivoff](https://github.com/yivi))
- German

Latest Files on GitHub: [https://github.com/mcguffin/wp-recaptcha-integration](https://github.com/mcguffin/wp-recaptcha-integration)

= Compatibility =

On a **WP Multisite** you can either activate the plugin network wide or on a single site.

Activated on a single site everything works as usual.

With network activation entering the API key and setting up where a recaptcha is required 
is up to the network admin. A blog admin can only select a theme and override the API key 
if necessary.


= Known Limitations =
- You can't have more than one old style reCaptcha on a page. This is a limitiation of 
  reCaptcha itself. If that's an issue for you, you should use the no Captcha Form.

- On a **Contact Form 7** when the reCaptcha is disabled (e.g. for logged in users) the field
  label will be still visible. This is due to CF7 Shortcode architecture, and can't be fixed.

  To handle this there is a filter `recaptcha_disabled_html`. You can return a message for your logged-in 
  users here. Check out the [GitHub Repo](https://github.com/mcguffin/wp-recaptcha-integration) for details.

- Old style reCaptchas do not work together with **WooCommerce**. 

- In **WooCommerce** the reset password form can not be protected by a captcha. To 
  overcome this restriction I requested a little change in the official WC repository, so there 
  is hope for a future version. See: (https://github.com/woothemes/woocommerce/pull/7029)

- There is no (and as far as one can see, there will never be) support for the **MailPoet**
  subscription form.

== Installation ==

First follow the standard [WordPress plugin installation procedere](http://codex.wordpress.org/Managing_Plugins).

Then go to the [Google Recaptcha Site](http://www.google.com/recaptcha), sign up your site and enter your API-Keys on the configuration page.

== Frequently asked questions ==

= Will you support plugin XYZ? =

If XYZ stands for a widely used free and OpenSource plugin in active development with some 
100k+ downloads I will give it a try. Just ask. 

= The captcha does not show up. What’s wrong? =

On the plugin settings page check out if the option “Disable for known users” is activated (it is by default).
Then log out (or open your page in a private browser window) and try again. 

If only the comment form is affected, it is very likely that your Theme does not use the 
`comment_form_defaults` filter. (That‘s where I add the captcha HTML, to make it appear 
right before the submit button.) You will have to use another hook, e.g. `comment_form_after_fields`.

Here is some code that will fix it:

- Go to (https://gist.github.com/mcguffin/97d7f442ee3e92b7412e)
- Click the "Download Gist" button
- Unpack the `.tar.gz` file.
- Create a zip Archive out of the included file `recaptcha-comment-form-fix.php` and name it `recaptcha-comment-form-fix.zip`.
- Install and activate it like any other WordPress plugin

If the problem still persist, Houston really has a problem, and you are welcome to post a support request. 

= Disabled submit buttons should be grey! Why aren't they? =

Very likely the Author of your Theme didn't care that a non functinal form element should 
look different than a functional one. This how you can overcome that issue: 

- Go to (https://gist.github.com/mcguffin/7cbfb0dab73eb32cb4a2)
- Click the "Download Gist" button
- Unpack the `.tar.gz` file.
- Create a zip Archive out of the included file `grey-out-disabled.php` and name it `grey-out-disabled.zip`.
- Install and activate it like any other WordPress plugin

= I want my visitors to solve only one Captcha and then never again. Is that possible? =

Yes. You can store in a session if a captcha was solved, and use the `wp_recaptcha_required` 
filter to supress further captchas. See (https://github.com/mcguffin/wp-recaptcha-integration#real-world-example) 
for a code example.

= I found a bug. Where should I post it? =

I personally prefer GitHub but you can post it in the forum as well. The plugin code is here: [GitHub](https://github.com/mcguffin/wp-recaptcha-integration)

= I want to use the latest files. How can I do this? =

Use the GitHub Repo rather than the WordPress Plugin. Do as follows:

1. If you haven't already done: [Install git](https://help.github.com/articles/set-up-git)

2. in the console cd into Your 'wp-content/plugins´ directory

3. type `git clone git@github.com:mcguffin/wp-recaptcha-integration.git`

4. If you want to update to the latest files (be careful, might be untested with your WP-Version) type `git pull.

Please note that the GitHub repository is more likely to contain unstable and untested code. Urgent fixes 
concerning stability or security (like crashes, vulnerabilities and alike) are more likely to be fixed in 
the official WP plugin repository first.

= I found a bug and fixed it. How can I contribute? =

Either post it on [GitHub](https://github.com/mcguffin/wp-recaptcha-integration) or—if you are working on a cloned repository—send me a pull request.

= Will you accept translations? =

Yep sure! (And a warm thankyou in advance.) It might take some time until your localization 
will appear in an official plugin release, and it is not unlikely that I will have added 
or removed some strings in the meantime. 

As soon as there is a [public centralized repository for WordPress plugin translations](https://translate.wordpress.org/projects/wp-plugins) 
I will migrate all the translation stuff there.

== Screenshots ==

1. Plugin Settings
2. Ninja Form Integration
3. Contact Form 7 Integration

== Changelog ==

= 1.0.9 =
- Fix: Preserve PHP 5.2 compatibility

= 1.0.8 =
- Feature: Individually set captcha theme in CF7 and Ninja forms (NoCaptcha only, old recaptcha not supported)
- Fix: PHP Warning in settings.
- Fix: PHP Fatal when check a old reCaptcha.
- Fix: js error with jQuery not present
- Fix: woocommerce checkout
- L10n: add Spanish

= 1.0.7 =
- Fix: Fatal error in settings
- Fix: messed up HTML comments
- Code: Put NinjaForms + CF7 handling into singletons

= 1.0.6 =
- Code: separate classes for recaptcha / nocaptcha
- Code: Class autoloader
- Fix: avoid double verification
- Fix: CF7 4.1 validation

= 1.0.5 =
- Add Language option
- Brasilian Portuguese localization
- Fix: conditionally load recaptcha lib.
- Fix: js error after cf7 validation error.

= 1.0.4 =
- Add WooCommerce Support (checkout page)
- Multisite: protect signup form as well.
- Reset noCaptcha after ajax calls (enhance compatibility with Comment Form Ajax plugin)
- Fix: incorrect redirect after saving Network settings

= 1.0.3 =
- Add BuddyPress support
- Action hook for wp_recaptcha_checked
- NoCaptcha: add non-js fallback.
- Code: pass `WP_Error` to `wp_die()` when comment captcha fails.
- Code: Rename filters recaptcha_required &gt; wp_recaptcha_required and recaptcha_disabled_html &gt; wp_recaptcha_disabled_html 
- Happy New Year!

= 1.0.2 =
- Feature: option to disable submit button, until the captcha is solved
- Rearrange comment form (put captcha above submit button)
- Fix: NoCaptcha did not refresh after submitting invalid ninja form via ajax

= 1.0.1 =
- Fix API Key test
- Fix theme select

= 1.0.0 =
- Allow more than one no Captcha per page
- Test captcha verification in Settings
- Multisite support.

= 0.9.1 =
- Add testing tool for checking the api key.
- Fixes

= 0.9.0 =
Initial Release

== Plugin API ==

The plugin offers some filters to allow themes and other plugins to hook in.

See [GitHub-Repo](https://github.com/mcguffin/wp-recaptcha-integration) for details.
