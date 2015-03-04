<?php
// Template Name: Referral Form

/**
 * Returns a templated form control.
 *
 * @param $id
 * @param $label
 * @param $type
 * @return string
 */
function form_control( $id, $label, $type ) {
    ob_start(); ?>
    <div class="form-control">
        <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
        <input type="<?php echo $type; ?>" id="<?php echo $id; ?>" name="<?php echo $id; ?>">
    </div>
    <?php return ob_get_clean();
}

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
    echo form_control( $id_prefix . '-fname', 'First Name', 'text' );
    echo form_control( $id_prefix . '-lname', 'Family Name', 'text' );
    return ob_get_clean();
}

/**
 * Returns a form snippet for capturing the date of birth.
 *
 * @param $id_prefix
 * @return string
 */
function form_control_dob( $id_prefix ) {
    ob_start(); ?>
    <div class="form-control">
        <?php $day = $id_prefix . "-dob-day"; ?>
        <label for="<?php echo $day; ?>">Birth Day</label>
        <select id="<?php echo $day; ?>" name="<?php echo $day; ?>">
            <?php for( $i = 1; $i <= 31; $i++): ?>
            <option><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="form-control">
        <?php $month = $id_prefix . "-dob-month"; ?>
        <label for="<?php echo $month; ?>">Birth Month</label>
        <select id="<?php echo $month; ?>" name="<?php echo $month; ?>">
            <?php for( $i = 1; $i <= 12; $i++): ?>
            <option><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="form-control">
        <?php $year = $id_prefix . "-dob-year"; ?>
        <label for="<?php echo $year; ?>">Birth Year</label>
        <select id="<?php echo $year; ?>" name="<?php echo $year; ?>">
            <?php for( $i = date("Y"); $i >= date("Y") - 30; $i--): ?>
                <option><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <?php return ob_get_clean();
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
                <form id="referral-form" class="flex-form">

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
                                        <?php echo form_control_dob( 'patient' ); ?>
                                    </div>
                                    <div class="row">
                                        <div class="form-control">
                                            <label for="patient-sex">Sex</label>
                                            <select id="patient-sex" name="patient-sex">
                                                <option>Male</option>
                                                <option>Female</option>
                                            </select>
                                        </div>
                                        <div class="form-control">
                                            <label for="patient-height">Height (cm)</label>
                                            <input id="patient-height" name="patient-height" type="number" min="1" max="300" required>
                                        </div>
                                        <div class="form-control">
                                            <label for="patient-weight">Weight (kg)</label>
                                            <input id="patient-weight" name="patient-weight" type="number" min="1" max="1000" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-control">
                                            <label for="patient-address">Address of Patient:</label>
                                            <textarea id="patient-address" name="patient-address" rows="5"></textarea>
                                        </div>
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
                                        <?php echo checkbox( 'patient-understand-english', 'Does the patient understand English?', 'alignright' ); ?>
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
                                        <div class="form-control radio-group">
                                            <label>Does the patient have a mother?</label>
                                            <div class="input-group">
                                                <input id="patient-mother-has-yes" type="radio" name="has-mother" value="Yes" checked> <label for="patient-mother-has-yes">Yes</label>
                                                <input id="patient-mother-has-no" type="radio" name="has-mother" value="No"> <label for="patient-mother-has-no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="#patient-mother-optional-group">
                                        <div class="row">
                                            <?php echo form_control_first_last_name( 'patient-mother' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo form_control_dob( 'patient-mother' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo form_control_religion( 'patient-mother' ); ?>
                                            <?php echo form_control( 'patient-mother-occupation', 'Occupation', 'text' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo form_control_languages( 'patient-mother' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo checkbox( 'patient-mother-understand-english', 'Does the mother understand English?', 'alignright' ) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-1-2">
                                <div class="heading padded">
                                    <h2>Father</h2>
                                </div>
                                <div class="shaded padded">
                                    <div class="row">
                                        <div class="form-control radio-group">
                                            <label>Does the patient have a father?</label>
                                            <div class="input-group">
                                                <input id="patient-father-has-yes" type="radio" name="has-father" value="Yes" checked> <label for="patient-father-has-yes">Yes</label>
                                                <input id="patient-father-has-no" type="radio" name="has-father" value="No"> <label for="patient-father-has-no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="#patient-father-optional-group">
                                        <div class="row">
                                            <?php echo form_control_first_last_name( 'patient-father' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo form_control_dob( 'patient-father' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo form_control_religion( 'patient-father' ); ?>
                                            <?php echo form_control( 'patient-father-occupation', 'Occupation', 'text' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo form_control_languages( 'patient-father' ); ?>
                                        </div>
                                        <div class="row">
                                            <?php echo checkbox( 'patient-father-understand-english', 'Does the father understand English?', 'alignright' ) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Step 3: Supporting Documentation</legend>
                        <div class="heading padded">
                            <h2>Patient Photographs</h2>
                        </div>
                        <div class="row spacing shaded padded">
                            <div class="col col-1-3">
                                <div class="form-control">
                                    <label for="patient-photo-1-input">Patient Photo #1</label>
                                    <img id="patient-photo-1" src="#" alt="Patient Photo Preview" class="aligncenter">
                                    <input type='file' id="photo-photo-1-input">
                                </div>
                            </div>
                            <div class="col col-1-3">
                                <div class="form-control">
                                    <label for="patient-photo-2-input">Patient Photo #2</label>
                                    <img id="patient-photo-2" src="#" alt="Patient Photo Preview" class="aligncenter">
                                    <input type='file' id="patient-photo-2-input">
                                </div>
                            </div>
                            <div class="col col-1-3">
                                <div class="form-control">
                                    <label for="patient-photo-3-input">Patient Photo #3</label>
                                    <img id="patient-photo-3-preview" src="#" alt="Patient Photo Preview" class="aligncenter">
                                    <input type='file' id="patient-photo-3-input">
                                </div>
                            </div>
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
