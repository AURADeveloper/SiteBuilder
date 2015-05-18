<?php
// Template Name: Referral Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // determine the url, convient conditional here for local testing
    if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {
        $url = 'http://staging.romac-patient-db.appspot.com/rest/refer';
    } else {
        $url = 'http://localhost:8080/rest/refer';
    }

    // the payload will be a json object submitted by the form
    $request_body = file_get_contents( 'php://input' );

    // prepare and execute a http request targeting the ehr referral endpoint
    $options = array(
        'http' => array(
            'header' => 'Content-type: application/json',
            'method' => 'POST',
            'content' => $request_body
        ),
    );
    $context = stream_context_create( $options );

    ob_start(); // prevent file_get_contents modifying header
    $result = file_get_contents( $url, false, $context );
    ob_end_clean();

    //
    // now we want to basically relay to response back to the client
    // because php lacks convenient api, some helper functions have been included below
    //

    // extracts a variable from the response header, does not work for the status (see below)
    function getResponseHeader($header) {
        foreach ($http_response_header as $key => $r) {
            if (stripos($r, $header) !== FALSE) {
                list($headername, $headervalue) = explode(":", $r, 2);
                return trim($headervalue);
            }
        }
    }

    // extracts the response code from the header
    $code_match = array();
    preg_match( '#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $code_match );

    // prep work done, now the actual response
    header( $code_match[0] );
    echo $result;
    exit;
}

/**
 * Returns a templated form control.
 *
 * @param $id
 * @param $label
 * @param $type
 * @param $args array an array of additional attributes to assign to the element
 * @return string
 */
function form_control( $id, $name, $label, $type, $args = array() ) {
    ob_start(); ?>
    <div class="form-control">
        <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
        <input type="<?php echo $type; ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo element_attributes( $args ); ?>>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a PHP array as a XML attribute list.
 *
 * @param $args
 * @return string
 */
function element_attributes( $args ) {
    ob_start(); ?>
    <?php
    foreach ($args as $k => $v) {
        echo " ";
        echo $k;
        if ($v != null) {
            echo '=';
            echo '"';
            echo $v;
            echo '"';
        }
    }
    ?>
    <?php return ob_get_clean();
}

/**
 * Returns a checkbox form control.
 *
 * @param $id
 * @param $label
 * @param null $class
 * @return string
 */
function checkbox( $id, $name, $label, $class = null ) {
    ob_start(); ?>
    <div class="form-control checkbox">
        <label for="<?php echo $id; ?>"<?php if ( !is_null( $class ) ): ?> class="<?php echo $class; ?>"<?php endif; ?>>
            <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>">
            <?php echo $label; ?>
        </label>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a form snippet for capturing the first and last name.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_first_last_name( $id_prefix ) {
    ob_start();
    echo form_control( $id_prefix . '-firstName', $id_prefix . '[firstName]', 'First Name', 'text', array(
        "required" => null,
        'pattern' => '.{2,32}',
        'title' => '2 to 32 characters')
    );
    echo form_control( $id_prefix . '-lastName', $id_prefix . '[lastName]', 'Family Name', 'text', array(
        'required' => null,
        'pattern' => '.{2,32}',
        'title' => '2 to 32 characters')
    );
    return ob_get_clean();
}

/**
 * Returns a form snippet for capturing a persons nationality.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_nationality( $id_prefix, $args = array()  ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_nationalities' );
    ob_start(); ?>
    <div class="form-control">
        <?php $id = $id_prefix . '-nationality'; ?>
        <?php $name = $id_prefix . '[nationality]'; ?>
        <label for="<?php echo $id; ?>">Nationality</label>
        <select type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo element_attributes( $args ); ?>>
            <option value=""></option>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a form snippet for capturing a persons country of origin.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_country( $id_prefix, $args = array() ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_countries ORDER BY preferred DESC' );
    ob_start(); ?>
    <div class="form-control">
        <?php $id = $id_prefix . '-countryOfOrigin'; ?>
        <?php $name = $id_prefix . '[countryOfOrigin]'; ?>
        <label for="<?php echo $id; ?>">Country of Origin</label>
        <select type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo element_attributes( $args ); ?>>
            <option value=""></option>
            <?php $last_pref = null; ?>
            <?php foreach( $results as $row ):
                if ( $last_pref != $row->preferred ) {
                    if ( $last_pref != null) {
                        echo '</optgroup>';
                    }
                    if ( $row->preferred == true ) {
                        echo '<optgroup label="Preferred">';
                    } else {
                        echo '<optgroup label="Other">';
                    }
                    $last_pref = $row->preferred;
                } ?>
                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
            <?php endforeach;
                echo '</optgroup>'; ?>
        </select>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a form snippet for capturing a persons industry.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_industry( $id_prefix, $args = array() ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_industry' );
    ob_start(); ?>
    <div class="form-control">
        <?php $id = $id_prefix . '-occupation'; ?>
        <?php $name = $id_prefix . '[occupation]'; ?>
        <label for="<?php echo $id; ?>">Occupation</label>
        <select type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo element_attributes( $args ); ?>>
            <option value=""></option>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a form snippet for capturing a persons religion.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_religion( $id_prefix, $args = array()  ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_religions' );
    ob_start(); ?>
    <div class="form-control">
        <?php $id = $id_prefix . '-religion'; ?>
        <?php $name = $id_prefix . '[religion]'; ?>
        <label for="<?php echo $id; ?>">Religion</label>
        <select type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo element_attributes( $args ); ?>>
            <option value=""></option>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a form snippet for capturing a persons spoken languages.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_languages( $id_prefix, $args = array() ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_languages' );
    ob_start(); ?>
    <div class="form-control">
        <?php $id = $id_prefix . '-languagesSpoken'; ?>
        <?php $name = $id_prefix . '[languagesSpoken]'; ?>
        <label for="<?php echo $id; ?>">Language/s Spoken</label> <p class="help-text">Please choose one (or more)</p>
        <select type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo element_attributes( $args ); ?>>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a radio control with yes no options only.
 *
 * @param $id
 * @param $label
 * @param null $default
 * @return string
 */
function form_control_yes_no( $id, $name, $label, $default = null, $args = array() ) {
    ob_start(); ?>
    <div class="form-control radio-group">
        <label><?php echo $label; ?></label>
        <div class="input-group">
            <?php $yes_id = $id . '-yes'; ?>
            <input id="<?php echo $yes_id; ?>" type="radio" name="<?php echo $name; ?>" value="true"<? if ($default == "Yes"): ?> checked<?php endif; ?><?php echo element_attributes( $args ); ?>> <label for="<?php echo $yes_id; ?>"> Yes</label>
            <?php $no_id = $id . '-no'; ?>
            <input id="<?php echo $no_id; ?>" type="radio" name="<?php echo $name; ?>" value="false"<? if ($default == "No"): ?> checked<?php endif; ?><?php echo element_attributes( $args ); ?>> <label for="<?php echo $no_id; ?>"> No</label>
        </div>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a address field.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_address( $id_prefix ) {
    ob_start(); ?>
    <div class="form-control">
        <?php $id = $id_prefix . '-address'; ?>
        <?php $name = $id_prefix . '[address]'; ?>
        <label for="<?php echo $id ?>">Address:</label>
        <textarea id="<?php echo $id ?>" name="<?php echo $name ?>" rows="5" required pattern=".{2,256}" required></textarea>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a field collection for a person type.
 *
 * @param $id_prefix
 * @param $singular
 * @return string
 */
function form_person( $id_prefix, $singular ) {
    ob_start(); ?>
    <div class="row">
        <?php echo form_control_first_last_name( $id_prefix ); ?>
    </div>
    <div class="row">
        <?php echo form_control( $id_prefix . '-dateOfBirth', $id_prefix . '[dateOfBirth]', 'Date of Birth', 'date', array( 'required' => null ) ); ?>
    </div>
    <div class="row">
        <?php echo form_control_address( $id_prefix ); ?>
    </div>
    <div class="row">
        <?php echo form_control( $id_prefix . '-email', $id_prefix . '[email]', 'Email Address', 'email' ) ?>
    </div>
    <div class="row">
        <?php echo form_control( $id_prefix . '-homePhone', $id_prefix . '[homePhone]', 'Home Phone', 'tel' ) ?>
        <?php echo form_control( $id_prefix . '-mobilePhone', $id_prefix . '[mobilePhone]', 'Mobile Phone', 'tel' ) ?>
    </div>
    <div class="row">
        <?php echo form_control_religion( $id_prefix ); ?>
        <?php echo form_control_industry( $id_prefix ); ?>
    </div>
    <div class="row">
        <?php echo form_control_languages( $id_prefix, array( 'multiple' => null, 'required' => null ) ); ?>
    </div>
    <div class="row">
        <?php echo form_control_yes_no( $id_prefix . '-understandsEnglish', $id_prefix . '[understandsEnglish]', 'Does the ' . $singular . ' understand English?' ); ?>
    </div>
    <?php return ob_get_clean();
}

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
                <h1 class="title">
                    <?php //the_title(); ?>Patient Referral Form
                </h1>

                <div id="referral-preamble">
                    <?php the_content(); ?>
                </div>

                <div id="working" class="referral-message">
                    <p><img src="/wp-content/themes/romac/images/ajax-loader.gif" alt="Ajax Loading" /></p>
                    <p>Thank you, your referral is being submitted.</p>
                    <p>Please leave the Window open, you will be notified once this process is complete.</p>
                </div>

                <div id="confirmation" class="referral-message">
                    <p><i class="fa fa-check-square-o fa-5x"></i></p>
                    <p>
                        Thank you, your referral has been submitted.<br/>
                        Your reference number is: <strong id="romacId"></strong>
                    </p>
                    <p>You have been emailed a copy of this receipt.</p>
                    <p>
                        <button id="new-referral" style="margin-top: 1em;">New Referral</button>
                    </p>
                </div>

                <div id="error" class="referral-message">
                    <p><i class="fa fa-exclamation-triangle fa-5x"></i></p>
                    <p>There was an error submitting the referral form, please check your Internet connection and try again. If the problem persists, please contact the website administrator.</p>
                </div>

                <div id="referral-progress">
                    <ul></ul>
                </div>

                <form id="referral-form" class="flex-form" onsubmit="setFormSubmitting()" data-destroy="false">
                    <!-- Remember if the form has been started so the user does not have to agree to the agreement again. -->
                    <input type="hidden" id="started" name="started">

                    <fieldset>
                        <legend>Step 1: Patients Details</legend>
                        <div class="heading padded">
                            <h2>Patient Details</h2>
                            <p>Please provide the personal details of the referred patient, all fields are required.</p>
                        </div>
                        <div class="row-set shaded">
                            <div class="row">
                                <?php echo form_control_first_last_name( 'patient' ); ?>
                            </div>
                            <div class="row">
                                <div class="form-control">
                                    <label for="patient-gender">Gender</label>
                                    <select id="patient-gender" name="patient[gender]" required>
                                        <option></option>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row flat">
                                <div class="row">
                                    <?echo form_control_yes_no( 'patient-isDobKnown', 'patient[isDobKnown]', 'Is the Patients date of birth known?', 'Yes', array( 'required' => null ) ); ?>
                                </div>
                                <div id="patientDobKnown">
                                    <div class="row">
                                        <?php echo form_control( 'patient-dateOfBirth', 'patient[dateOfBirth]', 'Date of Birth', 'date' ); ?>
                                    </div>
                                    <div class="row">
                                        <?echo form_control_yes_no( 'patient-hasBirthCertificate', 'patient[hasBirthCertificate]', 'Does the Patient have a birth certificate?' ); ?>
                                    </div>
                                </div>
                                <div id="patientDobUnknown">
                                    <div class="row">
                                        <?php $maxYear = date("Y"); $minYear = $maxYear - 18; ?>
                                        <?php echo form_control( 'patient-yearOfBirth', 'patient[yearOfBirth]', 'Please estimate the patients year of birth', 'number', array( 'min' => $minYear, 'max' => $maxYear ) ); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-control">
                                    <label for="patient-height">Height (cm)</label>
                                    <input id="patient-height" name="patient[height]" type="number" min="10" max="250" required>
                                </div>
                                <div class="form-control">
                                    <label for="patient-weight">Weight (kg)</label>
                                    <input id="patient-weight" name="patient[weight]" type="number" min="1" max="100" required>
                                </div>
                            </div>
                            <div class="row">
                                <?php echo form_control_address( 'patient' ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control_country( 'patient', array( 'required' => null ) ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control_nationality( 'patient', array( 'required' => null ) ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control_religion( 'patient', array( 'required' => null ) ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control_languages( 'patient', array( 'multiple' => null, 'required' => null ) ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control_yes_no( 'patient-understandsEnglish', 'patient[understandsEnglish]', 'Does the patient understand English?' ); ?>
                            </div>
                        </div>

                        <div class="heading padded">
                            <h2>Patient Photographs</h2>
                            <p>Please include a portrait photograph of the patient and others that show the medical condition if feesible.</p>
                            <dl>
                                <dt>Max Files:</dt>
                                <dd>4 (instructions provided later if more required)</dd>
                                <dt>Accepted Formats:</dt>
                                <dd>PNG, JPEG</dd>
                                <dt>Max File Size:</dt>
                                <dd>512kb (72 dpi adequate)</dd>
                            </dl>
                        </div>
                        <div id="patient-photos" class="row-set shaded">
                            <div class="row">
                                <div class="row flat inputs">
                                    <div class="form-control">
                                        <input type='file' name="photos">
                                    </div>
                                </div>
                            </div>
                            <div class="row section-buttons">
                                <button type="button" id="add-photo">Add Photo</button>
                            </div>
                        </div>

                        <div class="heading padded" style="display: none">
                            <h2>Medical Documentation</h2>
                        </div>
                        <div id="patient-documents" class="row spacing shaded padded" style="display: none">
                            <div class="inputs">
                                <div class="form-control">
                                    <input type='file' name="documents">
                                </div>
                            </div>
                            <div class="row section-buttons">
                                <button type="button" id="add-document">Add Document</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 2: Family Details</legend>

                        <div class="heading padded">
                            <h2>Mothers Details</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="row clear-bg">
                                <?php echo form_control_yes_no( 'patient-hasMother', 'patient[hasMother]', 'Does the patient have a mother?' ); ?>
                            </div>
                            <div id="patient-mother-optional-group">
                                <?php echo form_person( 'mother', 'mother' ); ?>
                            </div>
                        </div>

                        <div class="heading padded">
                            <h2>Fathers Details</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="row clear-bg">
                                <?php echo form_control_yes_no( 'patient-hasFather', 'patient[hasFather]', 'Does the patient have a father?' ); ?>
                            </div>
                            <div id="patient-father-optional-group">
                                <?php echo form_person( 'father', 'father' ); ?>
                            </div>
                        </div>

                    </fieldset>

                    <fieldset>
                        <legend>Step 3: Person to Accompany Patient</legend>

                        <div class="heading padded">
                            <h2>Accompaniment</h2>
                            <p>If accepted, please nominate guardian would will accompany the patient when travelling.</p>
                            <p>ROMACs preference is the mother.</p>
                        </div>
                        <div class="row-set shaded">
                            <div class="row clear-bg">
                                <div class="form-control">
                                    <label for="patient-accompaniment">Who is accompanying the patient?</label>
                                    <select id="patient-accompaniment" name="accompaniment">
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div id="patient-accompaniment-optional-group">
                                <div class="row">
                                    <div class="form-control">
                                        <label for="other-relationship">Relationship to patient</label>
                                        <select id="other-relationship" name="other[relationship]">
                                            <option>Brother</option>
                                            <option>Sister</option>
                                            <option>Auntie</option>
                                            <option>Uncle</option>
                                            <option>Friend</option>
                                        </select>
                                    </div>
                                </div>
                                <?php echo form_person( 'other', 'accompaniment' ); ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 4: Source of Referral</legend>
                        <div class="heading padded">
                            <h2>Referrer</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="row">
                                <?php echo form_control( 'referrer-name', 'referrer[name]', 'Name of Person/Club/Organisation', 'text' ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control_address( 'referrer' ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control( 'referrer-homePhone', 'referrer[homePhone]', 'Office/Home Phone', 'tel' ); ?>
                                <?php echo form_control( 'referrer-mobilePhone', 'referrer[mobilePhone]', 'Mobile Phone', 'tel' ); ?>
                            </div>
                            <div class="row">
                                <?php echo form_control( 'referrer-email', 'referrer[email]', 'E-Mail Address', 'email' ); ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 5: Confirmation Submission</legend>
                        <p>Please review the details carefully to ensure the referral can be processed as fast as possible.</p>
                        <div class="heading padded">
                            <h2>Patient Details</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="col confirm-labels">
                                <div>
                                    <label>First Name:</label>
                                    <span id="patient-firstName-c"></span>
                                </div>
                                <div>
                                    <label>Last Name:</label>
                                    <span id="patient-lastName-c"></span>
                                </div>
                                <div>
                                    <label>Date of Birth:</label>
                                    <span id="patient-dateOfBirth-c"></span>
                                </div>
                                <div>
                                    <label>Sex:</label>
                                    <span id="patient-gender-c"></span>
                                </div>
                                <div>
                                    <label>Height (cm):</label>
                                    <span id="patient-height-c"></span>
                                </div>
                                <div>
                                    <label>Weight (kg):</label>
                                    <span id="patient-weight-c"></span>
                                </div>
                                <div>
                                    <label>Address:</label>
                                    <span id="patient-address-c"></span>
                                </div>
                                <div>
                                    <label>Country of Origin:</label>
                                    <span id="patient-countryOfOrigin-c"></span>
                                </div>
                                <div>
                                    <label>Nationality:</label>
                                    <span id="patient-nationality-c"></span>
                                </div>
                                <div>
                                    <label>Religion:</label>
                                    <span id="patient-religion-c"></span>
                                </div>
                                <div>
                                    <label>Languages Spoken:</label>
                                    <span id="patient-languagesSpoken-c"></span>
                                </div>
                                <div>
                                    <label>Understands English:</label>
                                    <span id="patient-understandsEnglish-c"></span>
                                </div>
                            </div>
                        </div>
                        <div class="heading padded">
                            <h2>Supporting Files</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="col confirm-labels">
                                <div>
                                    <label>Photos:</label>
                                    <span id="patient-photos-c"></span>
                                </div>
                                <div>
                                    <label>Documents:</label>
                                    <span id="patient-documents-c"></span>
                                </div>
                            </div>
                        </div>
                        <div class="heading padded">
                            <h2>Source of Referral</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="col confirm-labels">
                                <div>
                                    <label>Name:</label>
                                    <span id="referrer-name-c"></span>
                                </div>
                                <div>
                                    <label>Address:</label>
                                    <span id="referrer-address-c"></span>
                                </div>
                                <div>
                                    <label>Home Phone:</label>
                                    <span id="referrer-homePhone-c"></span>
                                </div>
                                <div>
                                    <label>Mobile Phone:</label>
                                    <span id="referrer-mobilePhone-c"></span>
                                </div>
                                <div>
                                    <label>Email:</label>
                                    <span id="referrer-email-c"></span>
                                </div>
                            </div>
                        </div>
                        <div class="heading padded">
                            <h2>Mothers Details</h2>
                        </div>
                        <div class="row-set shaded">
                            <div id="patient-mother-group-none-c" class="col confirm-labels">
                                <div>
                                    The patient has no mother.
                                </div>
                            </div>
                            <div id="patient-mother-group-c" class="col confirm-labels">
                                <div>
                                    <label>Has Mother:</label>
                                    <span id="patient-hasMother-c"></span>
                                </div>
                                <div>
                                    <label>First Name:</label>
                                    <span id="mother-firstName-c"></span>
                                </div>
                                <div>
                                    <label>Last Name:</label>
                                    <span id="mother-lastName-c"></span>
                                </div>
                                <div>
                                    <label>Date of Birth:</label>
                                    <span id="mother-dateOfBirth-c"></span>
                                </div>
                                <div>
                                    <label>Address:</label>
                                    <span id="mother-address-c"></span>
                                </div>
                                <div>
                                    <label>Email:</label>
                                    <span id="mother-email-c"></span>
                                </div>
                                <div>
                                    <label>Home Phone:</label>
                                    <span id="mother-homePhone-c"></span>
                                </div>
                                <div>
                                    <label>Mobile Phone:</label>
                                    <span id="mother-mobilePhone-c"></span>
                                </div>
                                <div>
                                    <label>Religion:</label>
                                    <span id="mother-religion-c"></span>
                                </div>
                                <div>
                                    <label>Occupation Industry:</label>
                                    <span id="mother-occupation-c"></span>
                                </div>
                                <div>
                                    <label>Language/s Spoken:</label>
                                    <span id="mother-languagesSpoken-c"></span>
                                </div>
                                <div>
                                    <label>Understands English:</label>
                                    <span id="mother-understandsEnglish-c"></span>
                                </div>
                            </div>
                        </div>

                        <div class="heading padded">
                            <h2>Fathers Details</h2>
                        </div>
                        <div class="row-set shaded">
                            <div id="patient-father-group-none-c" class="col confirm-labels">
                                <div>
                                    The patient has no father.
                                </div>
                            </div>
                            <div id="patient-father-group-c" class="col confirm-labels">
                                <div>
                                    <label>Has Father:</label>
                                    <span id="patient-hasFather-c"></span>
                                </div>
                                <div>
                                    <label>First Name:</label>
                                    <span id="father-firstName-c"></span>
                                </div>
                                <div>
                                    <label>Last Name:</label>
                                    <span id="father-lastName-c"></span>
                                </div>
                                <div>
                                    <label>Date of Birth:</label>
                                    <span id="father-dateOfBirth-c"></span>
                                </div>
                                <div>
                                    <label>Address:</label>
                                    <span id="father-address-c"></span>
                                </div>
                                <div>
                                    <label>Email:</label>
                                    <span id="father-email-c"></span>
                                </div>
                                <div>
                                    <label>Home Phone:</label>
                                    <span id="father-homePhone-c"></span>
                                </div>
                                <div>
                                    <label>Mobile Phone:</label>
                                    <span id="father-mobilePhone-c"></span>
                                </div>
                                <div>
                                    <label>Religion:</label>
                                    <span id="father-religion-c"></span>
                                </div>
                                <div>
                                    <label>Occupation Industry:</label>
                                    <span id="father-occupation-c"></span>
                                </div>
                                <div>
                                    <label>Language/s Spoken:</label>
                                    <span id="father-languagesSpoken-c"></span>
                                </div>
                                <div>
                                    <label>Understands English:</label>
                                    <span id="father-understandsEnglish-c"></span>
                                </div>
                            </div>
                        </div>

                        <div class="heading padded">
                            <h2>Accompaniment</h2>
                        </div>
                        <div class="row-set shaded">
                            <div class="col confirm-labels">
                                <div>
                                    <label>Who is accompanying the patient?</label>
                                    <span id="patient-accompaniment-c"></span>
                                </div>
                            </div>
                        </div>

                        <div class="heading padded">
                            <h2>Accompaniment Details</h2>
                        </div>
                        <div class="row-set shaded">
                            <div id="patient-accompaniment-group-none-c" class="col confirm-labels">
                                <div>
                                    As above.
                                </div>
                            </div>
                            <div id="patient-accompaniment-group-c" class="col confirm-labels">
                                <div>
                                    <label>Relationship:</label>
                                    <span id="other-relationship-c"></span>
                                </div>
                                <div>
                                    <label>First Name:</label>
                                    <span id="other-firstName-c"></span>
                                </div>
                                <div>
                                    <label>Last Name:</label>
                                    <span id="other-lastName-c"></span>
                                </div>
                                <div>
                                    <label>Date of Birth:</label>
                                    <span id="other-dateOfBirth-c"></span>
                                </div>
                                <div>
                                    <label>Address:</label>
                                    <span id="other-address-c"></span>
                                </div>
                                <div>
                                    <label>Email:</label>
                                    <span id="other-email-c"></span>
                                </div>
                                <div>
                                    <label>Home Phone:</label>
                                    <span id="other-homePhone-c"></span>
                                </div>
                                <div>
                                    <label>Mobile Phone:</label>
                                    <span id="other-mobilePhone-c"></span>
                                </div>
                                <div>
                                    <label>Religion:</label>
                                    <span id="other-religion-c"></span>
                                </div>
                                <div>
                                    <label>Occupation Industry:</label>
                                    <span id="other-occupation-c"></span>
                                </div>
                                <div>
                                    <label>Language/s Spoken:</label>
                                    <span id="other-languagesSpoken-c"></span>
                                </div>
                                <div>
                                    <label>Understands English:</label>
                                    <span id="other-understandsEnglish-c"></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div id="form-controls" class="referral-controls">
                        <button type="button" id="referral-begin">Begin</button>
                        <button type="button" id="referral-previous"><i class="fa fa-angle-left"></i> Back</button>
                        <button type="button" id="referral-submit">Submit</button>
                        <button type="button" id="referral-next">Next <i class="fa fa-angle-right"></i></button>
                        <div class="reset">
                            <a href="#" id="referral-reset">Clear All Form Data</a>
                        </div>
                    </div>
                </form>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
