<?php
// Template Name: Contact Form
get_header(); ?>
<?php
//function add_query_vars_filter( $vars ){
//    $vars[] = "recipient";
//    return $vars;
//}
//add_filter( 'query_vars', 'add_query_vars_filter' );

$id = $_GET['recipient'];
$addressed = $id != '';
if ( $addressed ) {
    $recipient_name = get_field('persons_name', $id);
    $recipient_role = get_field('persons_job_role_title', $id);
    $recipient_email = get_field('persons_email', $id);
}
?>
<div id="content" role="main" class="full-width">
    <div class="fusion-three-fifth three_fifth fusion-layout-column fusion-column spacing-no">
        <p>Thank you for your interest in contacting us.</p>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
            Your message has been sent.
            <?php var_dump($_POST); ?>
        <?php } ?>
        <form class="aura-contact-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h3>Your Contact Details</h3>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="name"">Name:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="name" type="text" name="name">
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="email">Email:</label>
            <input class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="email" type="text" name="email">

            <h3>Recipient</h3>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="recipient">Recipient:</label>
            <select class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" id="recipient" name="recipient">
                <option selected value="<?php echo $id; ?>"><?php echo $recipient_name; ?> (<?php echo $recipient_role; ?>)</option>
            </select>

            <h3>Enquiry</h3>
            <label class="fusion-one-fifth one_fifth fusion-layout-column fusion-column" for="message">Message:</label>
            <textarea class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" rows="5" id="message" name="message"></textarea>

            <h3>Email Subscriptions</h3>
            <div class="fusion-one-fifth one_fifth fusion-layout-column fusion-column"></div>
            <label class="fusion-four-fifth four_fifth fusion-layout-column fusion-column last" for="subscribe">
                <input type="checkbox" id="subscribe" name="subscribe"> Yes, I would like to receive emails from ROMAC.
            </label>

            <input type="submit" value="Send">
        </form>
    </div>
    <div class="fusion-two-fifth two_fifth fusion-layout-column fusion-column spacing-no last">
        <div class="aura-info-box">
            <h3>Direct Contact</h3>
            <div class="aura-info-box-body">
                Rotary Oceania Medical Aid for Children Ltd (ROMAC)<br/>
                ABN 17 101 370 003

                <h4>Phone</h4>
                <a href="/about-romac/our-team/brendan-porter/">Brendan Porter</a> - BOARD CHAIRMAN<br/>
                0488 768 279

                <h4>Address</h4>
                ROMAC<br/>
                PO Box 779<br/>
                Parramatta, NSW 2124<br/>
                Australia

                <h4>Email Contacts</h4>
                operations@romac.org.au

                <h4>ROMAC Personnel</h4>
                Board members and regional contacts are listed on the <a href="/about-romac/our-team">Our Team</a> page.
            </div>
        </div>

        <div class="aura-info-box">
            <h3></h3>
        </div>
    </div>
</div>
<?php get_footer(); ?>