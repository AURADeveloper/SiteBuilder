<?php
// Template Name: Referral Form

/**
 * Returns a templated form control.
 *
 * @param $id
 * @param $label
 * @param $type
 * @param $args array an array of additional attributes to assign to the element
 * @return string
 */
function form_control( $id, $label, $type, $args = array() ) {
    ob_start(); ?>
    <div class="form-control">
        <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
        <input type="<?php echo $type; ?>" id="<?php echo $id; ?>" name="<?php echo $id; ?>"<?php
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
        ?>>
    </div>
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
function checkbox( $id, $label, $class = null ) {
    ob_start(); ?>
    <div class="form-control checkbox">
        <label for="<?php echo $id; ?>"<?php if ( !is_null( $class ) ): ?> class="<?php echo $class; ?>"<?php endif; ?>>
            <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $id; ?>">
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
    echo form_control( $id_prefix . '-fname', 'First Name', 'text', array( "required" => null ) );
    echo form_control( $id_prefix . '-lname', 'Family Name', 'text', array( "required" => null ) );
    return ob_get_clean();
}

/**
 * Returns a form snippet for capturing a persons nationality.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_nationality( $id_prefix ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_nationalities' );
    ob_start(); ?>
    <div class="form-control">
        <?php $nationality = $id_prefix . '-nationality'; ?>
        <label for="<?php echo $nationality; ?>">Nationality</label>
        <select type="text" id="<?php echo $nationality; ?>" name="<?php echo $nationality; ?>">
            <option value="">-- select one --</option>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->name; ?>"><?php echo $row->name; ?></option>
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
function form_control_country( $id_prefix ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_countries' );
    ob_start(); ?>
    <div class="form-control">
        <?php $country = $id_prefix . '-country-of-origin'; ?>
        <label for="<?php echo $country; ?>">Country of Origin</label>
        <select type="text" id="<?php echo $country; ?>" name="<?php echo $country; ?>">
            <option value="">-- select one --</option>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->iso; ?>"><?php echo $row->name; ?></option>
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
function form_control_religion( $id_prefix ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_religions' );
    ob_start(); ?>
    <div class="form-control">
        <?php $religion = $id_prefix . '-religion'; ?>
        <label for="<?php echo $religion; ?>">Religion</label>
        <select type="text" id="<?php echo $religion; ?>" name="<?php echo $religion; ?>">
            <option value="">-- select one --</option>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->name; ?>"><?php echo $row->name; ?></option>
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
function form_control_languages( $id_prefix ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_languages' );
    ob_start(); ?>
    <div class="form-control">
        <?php $language = $id_prefix . '-languages-spoken'; ?>
        <label for="<?php echo $language; ?>">Language/s Spoken</label>
        <select type="text" id="<?php echo $language; ?>" name="<?php echo $language; ?>" multiple>
            <?php foreach($results as $row): ?>
                <option value="<?php echo $row->name; ?>"><?php echo $row->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php return ob_get_clean();
}

/**
 * Returns a radio control with yes no options only.
 *
 * @param $id_prefix
 * @param $label
 * @param null $default
 * @return string
 */
function form_control_yes_no( $id_prefix, $label, $default = null ) {
    ob_start(); ?>
    <div class="form-control radio-group">
        <label><?php echo $label; ?></label>
        <div class="input-group">
            <?php $yes_id = $id_prefix . '-yes'; ?>
            <input id="<?php echo $yes_id; ?>" type="radio" name="<?php echo $id_prefix; ?>" value="Yes"<? if ($default == "Yes"): ?> checked<?php endif; ?>> <label for="<?php echo $yes_id; ?>"> Yes</label>
            <?php $no_id = $id_prefix . '-no'; ?>
            <input id="<?php echo $no_id; ?>" type="radio" name="<?php echo $id_prefix; ?>" value="No"<? if ($default == "No"): ?> checked<?php endif; ?> <label for="<?php echo $no_id; ?>"> No</label>
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
        <label for="<?php echo $id ?>">Address:</label>
        <textarea id="<?php echo $id ?>" name="<?php echo $id ?>" rows="5" required></textarea>
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
        <?php echo form_control( $id_prefix . '-dob', 'Date of Birth', 'date', array( 'required' => null ) ); ?>
    </div>
    <div class="row">
        <?php echo form_control_address( $id_prefix ); ?>
    </div>
    <div class="row">
        <?php echo form_control( $id_prefix . '-email-address', 'Email Address', 'email' ) ?>
    </div>
    <div class="row">
        <?php echo form_control( $id_prefix . '-home-phone', 'Home Phone', 'tel' ) ?>
        <?php echo form_control( $id_prefix . '-mobile-phone', 'Mobile Phone', 'tel' ) ?>
    </div>
    <div class="row">
        <?php echo form_control_religion( $id_prefix ); ?>
        <?php echo form_control( $id_prefix . '-occupation', 'Occupation', 'text' ); ?>
    </div>
    <div class="row">
        <?php echo form_control_languages( $id_prefix ); ?>
    </div>
    <div class="row">
        <?php echo form_control_yes_no( $id_prefix . '-understand-english', 'Does the ' . $singular . ' understand English?' ); ?>
    </div>
    <?php return ob_get_clean();
}

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
                <h1 class="page-title">
                    <?php the_title(); ?> <span>Online Referral Form</span>
                </h1>

                <div id="referral-preamble">
                    <?php the_content(); ?>
                </div>
                <div id="referral-progress">
                    <ul></ul>
                </div>
                <form id="referral-form" class="flex-form" onsubmit="setFormSubmitting()">

                    <fieldset>
                        <legend>Step 1: Patients Details</legend>
                        <div class="heading padded">
                            <h2>Patient</h2>
                        </div>
                        <div class="shaded padded">
                            <div class="row spacing">
                                <div class="col col-1-2">
                                    <div class="row">
                                        <?php echo form_control_first_last_name( 'patient' ); ?>
                                    </div>
                                    <div class="row">
                                        <div class="form-control">
                                            <label for="patient-sex">Sex</label>
                                            <select id="patient-sex" name="patient-sex" required>
                                                <option></option>
                                                <option>Male</option>
                                                <option>Female</option>
                                            </select>
                                        </div>
                                        <?php echo form_control( 'patient-dob', 'Date of Birth', 'date', array( 'required' => null )); ?>
                                    </div>
                                    <div class="row">
                                        <div class="form-control">
                                            <label for="patient-height">Height (cm)</label>
                                            <input id="patient-height" name="patient-height" type="number" min="10" max="250" required>
                                        </div>
                                        <div class="form-control">
                                            <label for="patient-weight">Weight (kg)</label>
                                            <input id="patient-weight" name="patient-weight" type="number" min="1" max="300" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php echo form_control_address( 'patient' ); ?>
                                    </div>
                                </div>
                                <div class="col col-1-2">
                                    <div class="row">
                                        <?php echo form_control_country( 'patient' ); ?>
                                    </div>
                                    <div class="row">
                                        <?php echo form_control_nationality( 'patient' ); ?>
                                    </div>
                                    <div class="row">
                                        <?php echo form_control_religion( 'patient' ); ?>
                                    </div>
                                    <div class="row">
                                        <?php echo form_control_languages( 'patient' ); ?>
                                    </div>
                                    <div class="row">
                                        <?php echo form_control_yes_no( 'patient-understand-english', 'Does the patient understand English?' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 2: Family Details</legend>
                        <div class="row spacing">
                            <div class="col col-1-2">
                                <div class="heading padded">
                                    <h2>Mother</h2>
                                </div>
                                <div class="shaded padded">
                                    <div class="row">
                                        <?php echo form_control_yes_no( 'patient-has-mother', 'Does the patient have a mother?', 'Yes' ); ?>
                                    </div>
                                    <div id="patient-mother-optional-group">
                                        <?php echo form_person( 'patient-mother', 'mother' ); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-1-2">
                                <div class="heading padded">
                                    <h2>Father</h2>
                                </div>
                                <div class="shaded padded">
                                    <div class="row">
                                        <?php echo form_control_yes_no( 'patient-has-father', 'Does the patient have a father?', 'Yes' ); ?>
                                    </div>
                                    <div id="patient-father-optional-group">
                                        <?php echo form_person( 'patient-father', 'father' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 3: Person to Accompany Patient</legend>
                        <div class="row spacing">
                            <div class="col col-1-2">
                                <div class="heading padded">
                                    <h2>Accompaniment</h2>
                                </div>
                                <div class="shaded padded">
                                    <div class="row">
                                        <div class="form-control">
                                            <label for="patient-accompaniment">Who is accompanying the patient?</label>
                                            <select id="patient-accompaniment">
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="patient-accompaniment-optional-group" class="col col-1-2">
                                <div class="heading padded">
                                    <h2>Details</h2>
                                </div>
                                <div class="shaded padded">
                                    <div class="row">
                                        <div class="form-control">
                                            <label for="patient-accompaniment-connection">Relationship to patient</label>
                                            <select id="patient-accompaniment-connection">
                                                <option>Brother</option>
                                                <option>Sister</option>
                                                <option>Auntie</option>
                                                <option>Uncle</option>
                                                <option>Friend</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php echo form_person( 'patient-accompaniment', 'accompaniment' ); ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 4: Source of Referral</legend>
                        <div class="heading padded">
                            <h2>Referrer</h2>
                        </div>
                        <div class="row spacing shaded padded">
                            <div class="col col-1-2">
                                <div class="row">
                                    <?php echo form_control( 'patient-referrer-name', 'Name of Person/Club/Organisation', 'text' ); ?>
                                </div>
                                <div class="row">
                                    <?php echo form_control_address( 'patient-referrer' ); ?>
                                </div>
                            </div>
                            <div class="col col-1-2">
                                <div class="row">
                                    <?php echo form_control( 'patient-referrer-home-phone', 'Office/Home Phone', 'tel' ); ?>
                                </div>
                                <div class="row">
                                    <?php echo form_control( 'patient-referrer-mobile-phone', 'Mobile Phone', 'tel' ); ?>
                                </div>
                                <div class="row">
                                    <?php echo form_control( 'patient-referrer-email', 'E-Mail Address', 'email' ); ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 5: Supporting Documentation</legend>
                        <div class="heading padded">
                            <h2>Patient Photographs</h2>
                        </div>
                        <div class="row spacing shaded padded">
                            <div class="col col-1-3">
                                <div class="form-control">
                                    <label for="patient-photo-1-input">Patient Photo #1</label>
                                    <input type='file' id="patient-photo-1-input">
                                    <img id="patient-photo-1" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/blank.gif" alt="Patient Photo Preview" class="aligncenter">
                                </div>
                            </div>
                            <div class="col col-1-3">
                                <div class="form-control">
                                    <label for="patient-photo-2-input">Patient Photo #2</label>
                                    <input type='file' id="patient-photo-2-input">
                                    <img id="patient-photo-2" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/blank.gif" alt="Patient Photo Preview" class="aligncenter">
                                </div>
                            </div>
                            <div class="col col-1-3">
                                <div class="form-control">
                                    <label for="patient-photo-3-input">Patient Photo #3</label>
                                    <input type='file' id="patient-photo-3-input">
                                    <img id="patient-photo-3-preview" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/blank.gif" alt="Patient Photo Preview" class="aligncenter">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 6: Confirmation</legend>
                        <div class="heading padded">
                            <h2>Confirm your Submission</h2>
                        </div>
                        <div class="row spacing shaded padded">

                        </div>
                    </fieldset>

                    <div class="referral-controls">
                        <button type="button" id="referral-begin">Begin</button>
                        <button type="button" id="referral-prev">Back</button>
                        <input type="submit" value="Submit">
                        <button type="button" id="referral-next">Next</button>
                    </div>
                </form>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
