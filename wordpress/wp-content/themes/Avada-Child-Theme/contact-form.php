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
    $recipient_name = get_field( 'persons_name', $id );
    $recipient_role = get_field( 'persons_job_role_title', $id );
    $recipient_email = get_field( 'persons_email', $id );
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
<div id="content" role="main" class="full-width">
    <div class="fusion-one-fourth one_fourth fusion-layout-column fusion-column spacing-no">
        <div class="aura-info-box">
            <h3>Direct Contact</h3>
            <div class="aura-info-box-body">
                <p>Rotary Oceania Medical Aid for Children Ltd (ROMAC)</p>
                <p><abbr title="Australian Business Number">ABN</abbr>: 17 101 370 003</p>

                <h4>Phone</h4>
                <p>
                    <a href="/about-romac/our-team/brendan-porter/">Brendan Porter</a> - BOARD CHAIRMAN<br/>
                    0488 768 279
                </p>

                <h4>Address</h4>
                <p>
                    ROMAC<br/>
                    PO Box 779<br/>
                    Parramatta, NSW<br/>
                    Australia 2124
                </p>

                <h4>Email Contacts</h4>
                <p>
                    operations@romac.org.au
                </p>

                <h4>ROMAC Personnel</h4>
                <p>
                    Board members and regional contacts are listed on the <a href="/about-romac/our-team">Our Team</a> page.
                </p>
            </div>
        </div>

        <div class="aura-info-box">
            <h3>ROMAC in New Zealand</h3>
            <div class="aura-info-box-body">
                <p>
                    <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/New-Zealand-Flag-sm.png" alt="New Zealand Flag" class="pull-right" />
                    Visit the Children's Charity in New Zealand website to contact ROMAC in NZ.
                </p>
                <p>
                    <a href="http://www.charityforchildren.org.nz">www.charityforchildren.org.nz</a>
                </p>
            </div>
        </div>
    </div>
    <div class="fusion-three-fourth three_fourth fusion-layout-column fusion-column spacing-no last">
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
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="fname">First Name:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="fname" type="text" name="fname" value="<?php echo $fname; ?>" required>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="lname">Last Name:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="lname" type="text" name="lname" value="<?php echo $lname; ?>" required>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="email">Email:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="email" type="email" name="email" value="<?php echo $email; ?>" placeholder="Please provide an email where we can reply to you" required>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="phone">Telephone:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="phone" type="tel" name="phone" value="<?php echo $phone; ?>" placeholder="You may optionally provide us your phone number for correspondence">

            <hr>

            <h3>Enquiry</h3>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="recipient">Recipient:</label>
            <select class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="recipient" name="recipient" required>
                <?php if ( $addressed ): ?>
                <option selected value="<?php echo $id; ?>"><?php echo $recipient_name; ?> (<?php echo $recipient_role; ?>)</option>
                <?php endif; ?>
                <?php if ( ! $addressed ):
                    $args = array( 'post_type' => array( 'person' ), 'nopaging' => true );
                    $query = new WP_Query( $args );
                    while ( $query->have_posts() ):
                        $query->the_post();
                        $selected = '';
                        if ( $query->post->ID == $default_recipient) $selected = ' selected';
                        echo '<option value="' . $query->post->ID . '"' . $selected . '>' .
                                get_field( 'persons_name', $query->post->ID ) .
                                ' (' . get_field( 'persons_job_role_title', $query->post->ID ) . ')' .
                                '</option>';
                    endwhile;
                endif; ?>
            </select>

            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="message">Message:</label>
            <textarea class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" rows="8" id="message" name="message" required><?php echo $message; ?></textarea>

            <hr>

            <h3>Email Subscriptions</h3>
            <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column"></div>
            <label class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" for="subscribe">
                <input type="checkbox" id="subscribe" name="subscribe" <?php echo $subscribe ? 'checked' : ''; ?>> Yes, I would like to receive emails from ROMAC.
            </label>

            <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column"></div>
            <div id="g-recaptcha" class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last"></div>

            <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column"></div>
            <div class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last">
                <input id="contact-form-submit" type="submit" value="Send" disabled>
            </div>
        </form>
    </div>
</div>

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

<?php get_footer(); ?>