<?php
// Template Name: Contact Form

// get the person id query param if one has been defined
$id = $_GET['recipient'];

// if defined, the contact form is considered addressed
// meaning, the message will be sent to a specific person
$addressed = $id != '';

// if addressed, get the persons meta data
if ( $addressed ) {
    $recipient_name = get_field( 'persons_name', $id );
    $recipient_role = get_field( 'persons_job_role_title', $id );
    $recipient_email = get_field( 'persons_email', $id );
}

// has the form been submitted? this flag will let us know
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

// the email body template
if ( $submitted ) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $recipient = 'sam.edge@auraaccess.com.au'; //get_field( 'persons_email', $_POST['recipient'] );

    $email_body = 'Sender: [fname] [lname]<br/>Email: [email]<br/><br/>[message]';
    $email_body = str_replace( '[fname]', $fname, $email_body );
    $email_body = str_replace( '[lname]', $lname, $email_body );
    $email_body = str_replace( '[email]', $email, $email_body );
    $email_body = str_replace( '[message]', $message, $email_body );

    $subject = 'ROMAC Website Enquiry from ' . $fname . ' ' . $lname;
    $email_result_success = wp_mail( $recipient, $subject, $email_body );
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
            if ( !$submitted ):
                echo 'Thank you for your interest in contacting us.';
            endif;

            if ( $submitted && $email_result_success ):
                echo '<div class="aura-msg-success">Thank you, your message has been sent.</div>';
            endif;

            if ( $submitted && !$email_result_success ):
                echo '<div class="aura-msg-failure">There was an error sending the email. If the issue persists, please contact support@romac.org.au</div>';
            endif;
            ?>
        </div>
        <form class="aura-contact-form" method="post"
              action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?' . $_SERVER['QUERY_STRING']); ?>">
            <h3>Your Contact Details</h3>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="fname">First Name:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="fname" type="text" name="fname">
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="lname">Last Name:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="lname" type="text" name="lname">
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="email">Email:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="email" type="email" name="email">
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="phone">Telephone:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="phone" type="tel" name="phone">

            <hr>

            <h3>Enquiry</h3>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="recipient">Recipient:</label>
            <select class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="recipient" name="recipient">
                <?php if ( $addressed ): ?>
                <option selected value="<?php echo $id; ?>"><?php echo $recipient_name; ?> (<?php echo $recipient_role; ?>)</option>
                <?php endif; ?>
                <?php if ( ! $addressed ):
                    $args = array( 'post_type' => array( 'person' ), 'nopaging' => true );
                    $query = new WP_Query( $args );
                    while ( $query->have_posts() ):
                        $query->the_post();
                        $selected = '';
                        if ( $query->post->ID == 134) $selected = ' selected';
                        echo '<option value="' . $query->post->ID . '"' . $selected . '>' .
                                get_field( 'persons_name', $query->post->ID ) .
                                ' (' . get_field( 'persons_job_role_title', $query->post->ID ) . ')' .
                                '</option>';
                    endwhile;
                endif; ?>
            </select>

            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="message">Message:</label>
            <textarea class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" rows="8" id="message" name="message"></textarea>

            <hr>

            <h3>Email Subscriptions</h3>
            <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column"></div>
            <label class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" for="subscribe">
                <input type="checkbox" id="subscribe" name="subscribe"> Yes, I would like to receive emails from ROMAC.
            </label>

            <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column"></div>
            <div class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last">
                <input type="submit" value="Send">
            </div>
        </form>
    </div>
</div>
<?php get_footer(); ?>