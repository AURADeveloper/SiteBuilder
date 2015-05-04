/**
 * A guard that prevents the leave page confirmation been shown if the form is being posted.
 * This variable is assigned to true on form submission.
 *
 * @type {boolean}
 */
var form_submitting = false;

/**
 * A flag that indicates the form has been started.
 * If it assigned true when the user clicks the begin referral button.
 *
 * @type {boolean}
 */
var form_started = false;

/**
 * A function that sets the form_submitting variable to true.
 * This is designed to be attached to the form element on the onsubmit attribute.
 * <code>
 *     <form onsubmit="setFormSubmitting"></form>
 * </code>
 */
var setFormSubmitting = function() {
    form_submitting = true;
};

/**
 * Attaches a leave page confirmation.<br/>
 * The user will be prompted if they wish to leave the page or not if the referral form has been started.
 *
 * DEPRECIATED: form state is now saved to local storage automatically
 */
//window.onload = function() {
//    window.addEventListener('beforeunload', function (e) {
//        var confirmationMessage = 'You have started a referral form submission. ';
//        confirmationMessage += 'If you leave before saving, your changes will be lost.';
//
//        if (form_submitting || !form_started) {
//            return undefined;
//        }
//
//        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
//        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
//    });
//};

// be safe - map $ to jQuery in a anonymous function
(function($) {

    /**
     * Bootstrap document ready.
     */
    $(document).ready(function() {
        /**
         * The index of the current, visible fieldset.
         * @type {number}
         */
        var current_fieldset = 0;

        /**
         * The index of the fieldset that contains the accompaniment fields.
         * @type {number}
         */
        var accompaniment_fieldset = 2;

        /**
         * Reference to the preamble element. This is hidden when the form is started.
         * @type {*|HTMLElement}
         */
        var preamable_panel = $('#referral-preamble');

        /**
         * Reference to the confirmation element. This is only displayed when the referral form has been submitted.
         * @type {*|HTMLElement}
         */
        var confirmation_panel = $( '#confirmation' );

        /**
         * Reference to the list that holds the numbered progress list.
         * @type {*|HTMLElement}
         */
        var progress_list = $('#referral-progress ul');

        /**
         * The collection of <li> elements that populate the progress list.
         * Each number in the list presents a fieldset/panel in the wizard. 1,2,3,4...
         * @type {*|HTMLElement}
         */
        var progress_items = null; // instantiated by init_steps()

        /**
         * The collection of all fieldsets in the referral form.
         * @type {*|HTMLElement}
         */
        var fieldsets = $('#referral-form fieldset');

        /**
         * The form submit button.
         * Pressing the button will cause the form to post.
         * @type {*|HTMLElement}
         */
        var submit_button = $('#submit');

        /**
         * The form begin button.
         * Pressing the button will skip the preamble to the first fieldset.
         * @type {*|HTMLElement}
         */
        var begin_button = $('#referral-begin');

        /**
         * The form previous button.
         * Pressing the button will step back to the previous fieldset.
         * @type {*|HTMLElement}
         */
        var previous_button = $('#referral-prev');

        /**
         * The form next button.
         * Pressing the button will processed to the next fieldset.
         * @type {*|HTMLElement}
         */
        var next_button = $('#referral-next');

        /**
         * The accompaniment select element.
         * Options (mother, father) are added and removed dynamically depending if they are present.
         * @type {*|HTMLElement}
         */
        var accompaniment_select = $('#patient-accompaniment');

        var working_panel = $( '#working' );

        /**
         * Checks if form has been started, applying the default state.
         * The form_started flag is returned, false meaning there should be no further states to evaluate.
         *
         * @returns {boolean} True if the form been started
         */
        function setFormDefaultState(form_started) {
            if (form_started) {
                preamable_panel.hide();
                begin_button.hide();
                submit_button.show();
                submit_button.attr('disabled', '');
                next_button.show();
                next_button.removeAttr('disabled');
                previous_button.show();
                previous_button.removeAttr('disabled');
                fieldsets.hide();
                fieldsets.eq(current_fieldset).show();
                progress_list.show();
                progress_items.each(function() {
                    $(this).removeClass();
                });
                progress_items.eq(current_fieldset).addClass('active');
            } else {
                // hide all none preamble elements
                fieldsets.hide();
                progress_list.hide();
                submit_button.hide();
                next_button.hide();
                previous_button.hide();
            }

            working_panel.hide();
            confirmation_panel.hide();

            return form_started;
        }

        /**
         * Populates the confirmation page, copying the fieldset values into the confirmation page.
         */
        function populateConfirmationFielset() {
            // iterate through all form inputs
            $('form input, form select, form textarea').each(function(index, element) {
                // get the name attribute of the context input element
                var id = $(element).attr('id');
                var name = $(element).attr('name');

                // corresponding confirm label
                var confirm_label = $('#' + id + '-c');

                // workaround for radio naming convention
                if ($(element).attr('type') == 'radio') {
                    id = id.replace('-yes', '');
                    id = id.replace('-no', '');
                    confirm_label = $('#' + id + '-c');
                }

                // add guard in-case input does not have a preview
                if (!confirm_label.length) return;

                // its a <select> element
                if ($(element).is('select')) {
                    // if its a multiple value select list, concat a comma seperated list
                    if ($(element).attr('multiple')) {
                        var foo = [];
                        $('select[name="' + name + '"] option:selected').each(function(i, selected) {
                            foo[i] = $(selected).text();
                        });
                        confirm_label.text(foo.join(', '));
                    }
                    // its just a single value
                    else {
                        confirm_label.text($('select[name="' + name + '"] option:selected').text());
                    }
                }
                // its a <textarea> element
                else if ($(element).is('textarea')) {
                    // replace cartridge returns with <br>
                    confirm_label.html($(element).val().replace(/\r\n|\r|\n/g, '<br/>'));

                }
                // it's a <input type="file"> element
                else if ($(element).attr('type') == 'file') {
                    // this is handled below
                }
                // it's a <input type="radio"> element
                else if ($(element).attr('type') == 'radio') {
                    // get the value of the checked radio only
                    if ($(element).attr('checked')) {
                        confirm_label.text($(element).next('label:first').html());
                    }
                }
                // it's all other inputs that require no special filtering
                else {
                    confirm_label.text($(element).val());
                }
            });

            // get the photo input
            var photo_label_c = $('#patient-photos-c');
            // clear photos label
            photo_label_c.empty();
            // iterate through the photo inputs
            $('input[type="file"][name^="patient-photo-"]').each(function(index, element) {
                // add guard, file could be blank
                if (element.files[0]) {
                    var filename = $(this).val().split('\\').pop();
                    var filesize = Math.ceil(element.files[0].size / 1024);
                    photo_label_c.append('<div>' + filename + ' (' + filesize + 'KB) </div>');
                }
            });
            if (photo_label_c.text() == '') {
                photo_label_c.text('There are no photos attached.');
            }

            // get the document input
            var document_label_c = $('#patient-documents-c');
            // clear documents label
            document_label_c.empty();
            // iterate through the photo inputs
            $('input[type="file"][name^="patient-document-"]').each(function(index, element) {
                // add guard, file could be blank
                if (element.files[0]) {
                    var filename = $(this).val().split('\\').pop();
                    var filesize = Math.ceil(element.files[0].size / 1024);
                    document_label_c.append('<div>' + filename + ' (' + filesize + 'KB) </div>');
                }
            });
            if (document_label_c.text() == '') {
                document_label_c.text('There are no documents attached.');
            }

            // hide or show the accompaniment depending if other is selected
            // if mother or father selected, hide this group because there is no new information
            if ($('#patient-accompaniment option:selected').text() == 'Other') {
                $('#patient-accompaniment-group-none-c').hide();
                $('#patient-accompaniment-group-c').show();
            } else {
                $('#patient-accompaniment-group-none-c').show();
                $('#patient-accompaniment-group-c').hide();
            }

            if ($('#patient-hasMother-yes').attr('checked')) {
                $('#patient-mother-group-none-c').hide();
                $('#patient-mother-group-c').show();
            } else {
                $('#patient-mother-group-none-c').show();
                $('#patient-mother-group-c').hide();
            }

            if ($('#patient-hasFather-yes').attr('checked')) {
                $('#patient-father-group-none-c').hide();
                $('#patient-father-group-c').show();
            } else {
                $('#patient-father-group-none-c').show();
                $('#patient-father-group-c').hide();
            }
        }

        /**
         * Evaluates the form state, populating the accompaniment select list with the mother and father depending if
         * they have been provided.
         */
        function populateAccompanimentSelect() {
            var hasMother = $('input[name="patient[hasMother]"]:checked').val();
            var hasMotherOpt = $('#patient-accompaniment option[value="mother"]');
            var hasFather = $('input[name="patient[hasFather]"]:checked').val();
            var hasFatherOpt = $('#patient-accompaniment option[value="father"]');

            if (hasMother == "true" && !hasMotherOpt.length) {
                accompaniment_select.append($('<option>', {
                    'value': 'mother',
                    'text': 'Mother',
                    'selected': ''
                }));
            }
            if (hasMother == "false" && hasMotherOpt.length) {
                hasMotherOpt.remove();
            }

            if (hasFather == "true" && !hasFatherOpt.length) {
                accompaniment_select.append($('<option>', {
                    'value': 'father',
                    'text': 'Father'
                }));
            }
            if (hasFather == "false" && hasFatherOpt.length) {
                hasFatherOpt.remove();
            }

            onAccompanySelectChange();
        }

        /**
         * Returns if the first fieldset is active.
         * @returns {boolean}
         */
        function isFirstFielsetActive() {
            return current_fieldset == 0;
        }

        /**
         * Returns if the confirmation (last) fieldset is active.
         * @returns {boolean}
         */
        function isConfirmationFieldsetActive() {
            return current_fieldset == fieldsets.size()-1;
        }

        /**
         * Returns if the accompaniment is active.
         * @returns {boolean}
         */
        function isAccompanimentFieldsetActive() {
            return current_fieldset == accompaniment_fieldset;
        }

        /**
         * The ping function evaluates the form state and modifies it accordingly.
         * This function is generally called when the user cycles through the form using the previous and next buttons.
         */
        function ping() {
            if (!setFormDefaultState(form_started)) {
                return;
            }

            if (isConfirmationFieldsetActive()) {
                populateConfirmationFielset();
                next_button.attr('disabled', '');
                submit_button.removeAttr('disabled');
            }

            if (isFirstFielsetActive()) {
                previous_button.attr('disabled', '');
            }

            if (isAccompanimentFieldsetActive()) {
                populateAccompanimentSelect();
            }
        }

        /**
         * Populates the wizard progress list with numbers corresponding to each fieldset.
         */
        function initialiseProgressList() {
            var i = 1;
            fieldsets.each(function() {
                progress_list.append('<li>' + i++ + '</li>');
            });
            progress_items = $('#referral-progress ul li');
        }

        /**
         * A change handler for the accompaniment select list.
         * If the other choice is selected, a fieldset to enter the accompaniments details is shown.
         * Otherwise, this is hidden because the mothers, fathers details have already been recorded.
         */
        function onAccompanySelectChange() {
            var val = accompaniment_select.val();
            if (val == "other") {
                $('#patient-accompaniment-optional-group').show();
            } else {
                $('#patient-accompaniment-optional-group').hide();
            }
        }

        /**
         * Reads a file typed input and assigns the encoded image src to the #patient-photo element.
         *
         * @param input The source input[type=file]
         */
        function readURL(input, imgElem) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $( imgElem ).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        /**
         * Shows or hides a group of inputs that is toggled by a paired radio input.
         *
         * @param input The radio input that shows/hides the group
         * @param groupId The corresponding group to toggle the visibility of
         */
        function showOptionalGroup(input, groupId) {
            if ($(input).val() == 'true') {
                $(groupId).show('slow');
            } else {
                $(groupId).hide('slow');
            }
        }

        /**
         * Validates a fieldset for invalid input.
         *
         * @param groupId {number} The index of the corresponding fielset
         * @returns {boolean} True if there are no validation errors
         */
        function validateFieldset( groupId ) {
            function validateField(fieldId) {
                if ( !referralForm.element( fieldId ) ) {
                    isValid = false;
                }
            }

            var referralForm = $( '#referral-form' ).validate({
                rules: {
                    "patient[dateOfBirth]": {
                        noOlderThan18Years: true,
                        required: true
                    }
                },
                messages: {
                    "patient[dateOfBirth]": {
                        noOlderThan18Years: "Patient must be < 18 years old",
                        required: "Please specify DOB"
                    }
                }
            });
            var isValid = true;

            switch ( groupId ) {
                case 0: // patient details
                    validateField( '#patient-firstName' );
                    validateField( '#patient-lastName' );
                    validateField( '#patient-dateOfBirth' );
                    validateField( '#patient-gender' );
                    validateField( '#patient-height' );
                    validateField( '#patient-weight' );
                    validateField( '#patient-address' );
                    validateField( '#patient-countryOfOrigin' );
                    validateField( '#patient-nationality' );
                    validateField( '#patient-religion' );
                    validateField( '#patient-languagesSpoken' );
                    validateField( 'input[name="patient[understandsEnglish]"' );
                    break;
            }

            return isValid;
        }

        /**
         * Adds a clicks handlers to the add photo button.
         * It will add a new line for a photo as long as there are no other empty inputs.
         */
        function onAddPhotoClick() {
            var currentInputs = $( "#patient-photos input" );
            if (currentInputs.last().val() != '') {
                var i = currentInputs.length;
                $("#patient-photos .inputs").append(
                    '<div class="form-control">' +
                        '<input type="file" name="patient-photo-input-' + i + '">' +
                    '</div>');
            }
        }

        /**
         * Adds a clicks handlers to the add document button.
         * It will add a new line for a document as long as there are no other empty inputs.
         */
        function onAddDocumentClick() {
            var currentInputs = $( "#patient-documents input" );
            if ( currentInputs.last().val() != '' ) {
                var i = currentInputs.length;
                $("#patient-documents .inputs").append(
                    '<div class="form-control">' +
                        '<input type="file" name="patient-document-input-' + i + '">' +
                    '</div>');
            }
        }

        /**
         * Referral form submit success Callback.
         */
        function submitSuccess(data) {
            working_panel.hide();
            fieldsets.hide();
            progress_list.hide();
            submit_button.hide();
            next_button.hide();
            previous_button.hide();

            $( '#romacId' ).text( data );
            confirmation_panel.show();
        }

        $.validator.addMethod("noOlderThan18Years", function(value, element) {
            var todaysDate = new Date();
            var dobDate = Date.parse( value );
            if ( isNaN(todaysDate) || isNaN(dobDate) ) {
                return false;
            }
            // 86400000 is one day in milliseconds
            // 6574.36 is how many days in 18 years (accounting for leaps)
            var eighteenYears = 86400000 * 6574.36;
            if (todaysDate - dobDate > eighteenYears) {
                return false;
            }
            return true;
        });


        //
        // Start Form instantiation
        //



        // attach a click handler to the begin button
        begin_button.click(function() {
            form_started = true;
            $( '#started' ).val( 'true' );
            ping();
        });

        // attach a click handler to the next button
        next_button.click(function() {
            if (!validateFieldset(current_fieldset)) {
                return;
            }
            if (current_fieldset < fieldsets.size()-1) {
                current_fieldset++;
                ping();
            }
        });

        // attach a click handler to the previous button
        previous_button.click(function() {
            if (current_fieldset > 0) {
                current_fieldset--;
                ping();
            }
        });

        // attach submit click action
        submit_button.click(function() {
            $( '#referral-form' ).hide();
            progress_items.hide();
            working_panel.show();

            var json = $( '#referral-form' ).serializeJSON();
            $.post( '/patients/refer-a-patient', json, submitSuccess );
        });

        // attach a change handler to the accompaniment select list
        accompaniment_select.change( onAccompanySelectChange );

        // attach handlers to the if has mother/father inputs
        $( "#patient-hasMother-yes" ).change(function() {
            showOptionalGroup( this, '#patient-mother-optional-group' );
        });
        $( "#patient-hasMother-no" ).change(function() {
            showOptionalGroup( this, '#patient-mother-optional-group' );
        });
        $( "#patient-hasFather-yes" ).change(function() {
            showOptionalGroup( this, '#patient-father-optional-group' );
        });
        $( "#patient-hasFather-no" ).change(function() {
            showOptionalGroup( this, '#patient-father-optional-group' );
        });

        /**
         * Instantiate sisyphus so that the form values are persisted to local-storage
         */
        $( '#referral-form' ).sisyphus({
            autoRelease: false // only clear form if post success
        });

        form_started = $( '#started' ).val() == 'true';

        /**
         * Instantiate special form controls
         */
        var select2LanguageOptions = { maximumSelectionLength: 2 };
        $( "#patient-languagesSpoken" ).select2( select2LanguageOptions );
        $( "#mother-languagesSpoken" ).select2( select2LanguageOptions );
        $( "#father-languagesSpoken" ).select2( select2LanguageOptions );
        $( "#accompaniment-languagesSpoken" ).select2( select2LanguageOptions );
        // country
        $( "#patient-countryOfOrigin" ).select2();
        $( "#mother-countryOfOrigin" ).select2();
        $( "#father-countryOfOrigin" ).select2();
        $( "#accompaniment-countryOfOrigin" ).select2();
        // nationality
        $( "#patient-nationality" ).select2();

        /**
         * Attaches click handlers to the 'add document' buttons.
         * This will dynamically add a new line for another document.
         */
        $( "#add-photo" ).click(onAddPhotoClick);
        $( "#add-document" ).click(onAddDocumentClick);

        /**
         * Dynamically generates the  1-2-3-4-5... steps based on fieldsets
         */
        initialiseProgressList();

        /**
         * Trigger a state refresh when the form is loaded.
         */
        ping();
    });
})( jQuery );