<?php
// Template Name: Contact Form

// state - tracks what action result of any action taken
define( 'DEFAULT_STATE', 0 );
define( 'SERVER_ERROR', 1 );
define( 'RECAPTCHA_FAIL', 2 );
define( 'SEND_MAIL_FAIL', 3 );
define( 'SEND_MAIL_SUCCESS', 4 );
$contact_state = constant( 'DEFAULT_STATE' );

// default recipient - the contact selected when no id param used
$default_recipient = 134;

// reCAPTCHA vars
$private_key = '6LcXHgITAAAAAF8xCFYT3YVBWwEsN8d53DS7uNSC';
$public_key = '6LcXHgITAAAAAPI0_I6uq1T_5sQO_8DQ76TZVpwF';
$site_verify_url = 'https://www.google.com/recaptcha/api/siteverify?secret=[SECRET]&response=[RESPONSE]';

// get the person id query param if one has been defined
$id = $_GET['recipient'];

// if defined, the contact form is considered addressed
//   meaning, the message will be sent to a specific person
$addressed = ($id != '');

// has the form been submitted? this flag will let us know
$submitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

// if addressed, get the persons meta data
if ( $addressed ) {
    $recipient_name = get_post_meta( $id, '_rot_name' )[0];
    $recipient_role = get_post_meta( $id, '_rot_role' )[0];
    $recipient_email = get_post_meta( $id, '_rot_email' )[0];
}

// verify the reCAPTCHA challenge
$recaptcha_success = FALSE;
// only verify if the form has been submitted
if ( $submitted ) {
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // ensure the reCAPTCHA response was included in the post body
    //   otherwise, this is an invalid state
    if ( $recaptcha_response != null && strlen( $recaptcha_response ) > 0 ) {
        // prepare the request
        $context = array(
            'https' => array (
                'method' => 'GET'
            )
        );

        $site_verify_url = str_replace( '[SECRET]', $private_key, $site_verify_url );
        $site_verify_url = str_replace( '[RESPONSE]', $recaptcha_response, $site_verify_url );
        $context = stream_context_create( $context );
        $result = file_get_contents( $site_verify_url, FALSE, $context );

        if ( !$result ) {
            $contact_state = constant( 'SERVER_ERROR' );
        } else {
            $decoded_response = json_decode( $result, TRUE );
            if ( $decoded_response['success'] == TRUE ) {
                $recaptcha_success = true;
            } else {
                $contact_state = constant( 'RECAPTCHA_FAIL' );
            }
        }

    } else {
        $contact_state = constant( 'RECAPTCHA_FAIL' );
    }
}

if ( $submitted ) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    $subscribe = $_POST['subscribe'];
    $recipient = 'sam.edge@auraaccess.com.au'; //get_field( 'persons_email', $_POST['recipient'] );
}

// the email body template
if ( $submitted && $recaptcha_success ) {
    $email_body = 'Sender: [fname] [lname]<br/>Email: [email]<br/><br/>[message]';
    $email_body = str_replace( '[fname]', $fname, $email_body );
    $email_body = str_replace( '[lname]', $lname, $email_body );
    $email_body = str_replace( '[email]', $email, $email_body );
    $email_body = str_replace( '[message]', $message, $email_body );

    $subject = 'ROMAC Website Enquiry from ' . $fname . ' ' . $lname;
    $email_result_success = wp_mail( $recipient, $subject, $email_body );
    if ( $email_result_success ) {
        $contact_state = constant( 'SEND_MAIL_SUCCESS' );

        // the message was sent successfully, un-setting these variables will
        //   ensure that the form is not pre-populated again
        unset( $fname );
        unset( $lname );
        unset( $email );
        unset( $phone );
        unset( $message );
        unset( $subscribe );
    } else {
        $contact_state = constant( 'SEND_MAIL_FAIL' );
    }
}

get_header();
?>
<div id="content" role="main" class="contact-page">
    <div class="contact-form">
        <div class="aura-msg">
            <?php
            switch ( $contact_state ) :
                case constant( 'DEFAULT_STATE' ):
                    echo 'Thank you for your interest in contacting us.';
                    break;
                case constant( 'SEND_MAIL_SUCCESS' ):
                    echo '<div class="aura-msg-success">Thank you, your message has been sent.</div>';
                    break;
                case constant( 'SEND_MAIL_FAIL' ):
                case constant( 'SERVER_ERROR' ):
                    echo '<div class="aura-msg-failure">There was an error sending the email. If the issue persists, please contact support@romac.org.au</div>';
                    break;
                case constant( 'RECAPTCHA_FAIL' ):
                    echo '<div class="aura-msg-failure">reCAPTCHA challenge failed, please try again.</div>';
                    break;
            endswitch; ?>
        </div>
        <form class="aura-contact-form" method="post"
              action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?' . $_SERVER['QUERY_STRING']); ?>">
            <h3>Your Contact Details</h3>
            <div class="control-group">
                <label for="fname">First Name:</label>
                <input id="fname" type="text" name="fname" value="<?php echo $fname; ?>" required>
            </div>
            <div class="control-group">
                <label for="lname">Last Name:</label>
                <input id="lname" type="text" name="lname" value="<?php echo $lname; ?>" required>
            </div>
            <div class="control-group">
                <label for="email">Email:</label>
                <input id="email" type="email" name="email" value="<?php echo $email; ?>" placeholder="Please provide an email where we can reply to you" required>
            </div>
            <div class="control-group">
                <label for="phone">Telephone:</label>
                <input id="phone" type="tel" name="phone" value="<?php echo $phone; ?>" placeholder="You may optionally provide us your phone number for correspondence">
            </div>
            <h3>Enquiry</h3>
            <div class="control-group">
                <label for="recipient">Recipient:</label>
                <select id="recipient" name="recipient" required>
                    <?php if ( $addressed ): ?>
                    <option selected value="<?php echo $id; ?>"><?php echo $recipient_name; ?> (<?php echo $recipient_role; ?>)</option>
                    <?php endif; ?>
<!--                    --><?php //if ( ! $addressed ):
//                        $args = array( 'post_type' => array( 'our_team' ), 'nopaging' => true );
//                        $query = new WP_Query( $args );
//                        while ( $query->have_posts() ):
//                            $post = $query->next_post();
//                            //$query->the_post();
//                            $selected = '';
//                            if ( $post->ID == $default_recipient) $selected = ' selected';
//                            echo '<option value="' . $post->ID . '"' . $selected . '>' .
//                                    get_post_meta( $post->ID, '_rot_name', true ) .
//                                    ' (' . get_post_meta( $post->ID, '_rot_role', true ) . ')' .
//                                    '</option>';
//                        endwhile;
//                    endif; ?>
                </select>
            </div>
            <div class="control-group">
                <label for="message">Message:</label>
                <textarea rows="8" id="message" name="message" required><?php echo $message; ?></textarea>
            </div>

            <div class="control-group offset">
                <div id="g-recaptcha"></div>
            </div>

            <div class="control-group offset">
                <label for="subscribe" class="checkbox">
                    <input type="checkbox" id="subscribe" name="subscribe" <?php echo $subscribe ? 'checked' : ''; ?>> Yes, I would like to receive emails from ROMAC.
                </label>
            </div>

            <div class="control-group offset">
                <input id="contact-form-submit" type="submit" value="Send" disabled>
            </div>
        </form>
        <!-- The Google reCAPTCHA form control -->
        <!-- It manually instantiates the widget so that the control can be validated. -->
        <!-- The form submit button will be disabled until the reCAPTCHA control is completed. -->
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script type="text/javascript">
            var onloadCallback = function() {
                grecaptcha.render('g-recaptcha', {
                    'sitekey': '<?php echo $public_key; ?>',
                    'callback': function(response) {
                        document.getElementById('contact-form-submit').removeAttribute('disabled');
                    }
                });
            };
        </script>
    </div>
</div>
<?php get_sidebar() ?>
<?php get_footer(); ?>