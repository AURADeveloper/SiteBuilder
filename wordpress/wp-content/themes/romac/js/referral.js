// be safe - map $ to jQuery in a anonymous function
(function($) {
    /**
     * No older than 18 years validator.
     * ---------------------------------
     *
     * Custom validator method for testing the supplied date is no greater than 18 years.
     */
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

    /**
     * Referral Form Controller Logic.
     * -------------------------------
     *
     * Dependencies: jQuery     (https://jquery.com/)
     *               sisyphus   (http://sisyphus-js.herokuapp.com/)
     *               validation (http://jqueryvalidation.org/)
     */
    var referralForm = function() {
        var config = {
            elements: {
                referralForm: '#referral-form',
                preambleElement: '#referral-preamble',
                confirmationElement: '#confirmation',
                workingElement: '#working',

                progressList: '#referral-progress ul',
                progressListItems: '#referral-progress ul li',

                beginButton: '#referral-begin',
                nextButton: '#referral-next',
                previousButton: '#referral-previous',
                submitButton: '#submit',

                patientDobKnown: '#patientDobKnown',
                patientDobUnknown: '#patientDobUnknown',
                addPhotoButton: '#add-photo',
                addDocumentButton: '#add-document',

                conditionsAcceptedInput: '#started'
            },
            fieldsets: {
                patient: {
                    firstName: '#patient-firstName',
                    lastName: '#patient-lastName',
                    isDobKnown: 'input[name="patient[isDobKnown]"]',
                    dateOfBirth: '#patient-dateOfBirth',
                    hasBirthCertificate: 'input[name="patient[hasBirthCertificate]"]',
                    gender: '#patient-gender',
                    height: '#patient-height',
                    weight: '#patient-weight',
                    address: '#patient-address',
                    countryOfOrigin: '#patient-countryOfOrigin',
                    religion: '#patient-religion',
                    languagesSpoken: '#patient-languagesSpoken',
                    understandsEnglish: 'input[name="patient[understandsEnglish]"]'
                },
                guardians: {
                    hasMother: 'input[name="patient[hasMother]"]',
                    motherFirstName: '#mother-firstName'
                },
                accompaniment: {

                },
                byIndex: function(index) {
                    switch (index) {
                        case config.patientFieldsetIndex:
                            return config.fieldsets.patient;
                        case config.guardianFieldsetIndex:
                            return config.fieldsets.guardians;
                        case config.accompanimentFieldsetIndex:
                            return config.fieldsets.accompaniment;
                        default:
                            return { };
                    }
                }
            },
            sisyphus: {
                autoRelease: false // manually clear form on successful submit
            },
            validation: {
                rules: {
                    "patient[dateOfBirth]": {
                        noOlderThan18Years: true,
                        required: true,
                        depends: function(element) {
                            return $( config.fieldsets.patient.isDobKnown + ":checked" ).val() == 'true';
                        }
                    },
                    "patient[hasBirthCertificate]": {
                        required: true,
                        depends: function(element) {
                            return $( config.fieldsets.patient.isDobKnown + ":checked" ).val() == 'true';
                        }
                    },
                    "patient[yearOfBirth]": {
                        required: true,
                        depends: function(element) {
                            return $( config.fieldsets.patient.isDobKnown + ":checked" ).val() == 'false';
                        }
                    }
                },
                messages: {
                    "patient[dateOfBirth]": {
                        noOlderThan18Years: "Patient must be < 18 years old",
                        required: "Please specify DOB"
                    }
                }
            },
            patientFieldsetIndex: 0,
            guardianFieldsetIndex: 1,
            accompanimentFieldsetIndex: 2,
            postUrl: '/patients/refer-a-patient'
        };

        var activeFieldsetIndex = 0;

        function init() {
            // enable persistence
            $( config.elements.referralForm ).sisyphus( config.sisyphus );

            // init routine
            initSelect2Lists();
            attachClickHandlers();
            attachChangeHandlers();
            buildProgressList();
            ping();
        }

        function attachClickHandlers() {
            // the begin referral button - accepts the terms and conditions
            $( config.elements.beginButton ).click( function() {
                $( config.elements.conditionsAcceptedInput ).val( 'true' );
                ping();
            } );

            // the next button - progresses to the next fieldset if the current is valid
            $( config.elements.nextButton ).click( function() {
                if ( validateCurrentFieldset() ) {
                    if ( !isLastFieldset() ) {
                        activeFieldsetIndex++;
                        ping();
                    }
                }
            } );

            // the previous button - moves back one fieldset
            $( config.elements.previousButton ).click( function() {
                if ( !isFirstFieldset() ) {
                    activeFieldsetIndex--;
                    ping();
                }
            } );

            // the submit button
            $( config.elements.submitButton ).click( function() {
                $( config.elements.referralForm ).hide();
                $( config.elements.progressList ).hide();
                $( config.elements.workingElement ).show();

                var json = $( config.elements.referralForm ).serializeJSON();
                $.post( config.postUrl, json, submitSuccess );
            } );

            // add another photo button
            $( config.elements.addPhotoButton ).click( function() {
                var currentInputs = $( "#patient-photos input" );
                if (currentInputs.last().val() != '') {
                    var i = currentInputs.length;
                    $("#patient-photos .inputs").append(
                        '<div class="form-control">' +
                        '<input type="file" name="patient-photo-input-' + i + '">' +
                        '</div>');
                }
            } );

            // add another document button
            $( config.elements.addDocumentButton ).click( function() {
                var currentInputs = $( "#patient-documents input" );
                if ( currentInputs.last().val() != '' ) {
                    var i = currentInputs.length;
                    $("#patient-documents .inputs").append(
                        '<div class="form-control">' +
                        '<input type="file" name="patient-document-input-' + i + '">' +
                        '</div>');
                }
            } );
        }

        function attachChangeHandlers() {
            // hide/show dob fields depending if dob is known or not
            $( config.fieldsets.patient.isDobKnown ).change( function(eventData, handler) {
                if (eventData.target.value == 'true') {
                    $( config.elements.patientDobKnown ).show();
                    $( config.elements.patientDobUnknown ).hide();
                    return;
                }
                if (eventData.target.value == 'false') {
                    $( config.elements.patientDobKnown ).hide();
                    $( config.elements.patientDobUnknown ).show();
                    return;
                }
                $( config.elements.patientDobKnown ).hide();
                $( config.elements.patientDobUnknown ).hide();
            } );

            $( '#patient-accompaniment' ).change( function() {
                var val = $( '#patient-accompaniment' ).val();
                if (val == "other") {
                    $('#patient-accompaniment-optional-group').show();
                } else {
                    $('#patient-accompaniment-optional-group').hide();
                }
            } );

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
        }

        function showOptionalGroup(input, groupId) {
            if ($(input).val() == 'true') {
                $(groupId).show('slow');
            } else {
                $(groupId).hide('slow');
            }
        }

        function initSelect2Lists() {
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
        }

        function buildProgressList() {
            var i = 1;
            $( 'fieldset' ).each(function() {
                $( config.elements.progressList ).append('<li>' + i++ + '</li>');
            });
        }

        function ping() {
            // always hide these, only shown when form submitted
            $( config.elements.workingElement ).hide();
            $( config.elements.confirmationElement ).hide();

            // if conditions are not accepted, set initial state - show conditions
            if (!areConditionsAccepted()) {
                // hide all none preamble elements
                $( 'fieldset' ).hide();
                $( config.elements.progressList ).hide();
                $( config.elements.submitButton ).hide();
                $( config.elements.nextButton ).hide();
                $( config.elements.previousButton ).hide();
                return;
            }

            // else, form has been started, configure it
            $( config.elements.preambleElement ).hide();

            // reset control button states
            $( config.elements.beginButton ).hide();
            $( config.elements.submitButton ).show();
            $( config.elements.submitButton ).attr('disabled', '');
            $( config.elements.nextButton ).show();
            $( config.elements.nextButton ).removeAttr('disabled');
            $( config.elements.previousButton ).show();
            $( config.elements.previousButton ).removeAttr('disabled');

            // only show the active fieldset
            var fieldsets = $( 'fieldset' );
            fieldsets.hide();
            fieldsets.eq( activeFieldsetIndex ).show();

            // highlight the active progress list item
            $( config.elements.progressList ).show();
            var progressListItems = $( config.elements.progressListItems );
            progressListItems.each(function() {
                $( this ).removeClass();
            });
            progressListItems.eq( activeFieldsetIndex ).addClass('active');

            $( config.fieldsets.patient.isDobKnown + ":checked" ).trigger( 'change' );

            if (isLastFieldset()) {
                populateConfirmationFields();
                $( config.elements.nextButton ).attr('disabled', '');
                $( config.elements.submitButton ).removeAttr('disabled');
            }

            if (isFirstFieldset()) {
                $( config.elements.previousButton ).attr('disabled', '');
            }

            if (activeFieldsetIndex == config.accompanimentFieldsetIndex) {
                populateAccompanimentSelect();
            }
        }

        function validateFieldset( groupId ) {
            var fieldset = config.fieldsets.byIndex( groupId );
            var validatedForm = $( config.elements.referralForm ).validate( config.validation );

            for (var property in fieldset) {
                if (fieldset.hasOwnProperty( property )) {
                    var value = fieldset[property];
                    if (!validatedForm.element( value )) {
                        return false;
                    }
                }
            }

            return true;
        }

        function populateConfirmationFields() {
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

        function populateAccompanimentSelect() {
            var selectList = $( '#patient-accompaniment' );
            var hasMother = $('input[name="patient[hasMother]"]:checked').val();
            var hasMotherOpt = $('#patient-accompaniment option[value="mother"]');
            var hasFather = $('input[name="patient[hasFather]"]:checked').val();
            var hasFatherOpt = $('#patient-accompaniment option[value="father"]');

            if (hasMother == "true" && !hasMotherOpt.length) {
                selectList.append($('<option>', {
                    'value': 'mother',
                    'text': 'Mother',
                    'selected': ''
                }));
            }
            if (hasMother == "false" && hasMotherOpt.length) {
                hasMotherOpt.remove();
            }

            if (hasFather == "true" && !hasFatherOpt.length) {
                selectList.append($('<option>', {
                    'value': 'father',
                    'text': 'Father'
                }));
            }
            if (hasFather == "false" && hasFatherOpt.length) {
                hasFatherOpt.remove();
            }

            selectList.trigger( 'change' );
        }

        function submitSuccess(data) {
            $( config.elements.workingElement ).hide();
            $( 'fieldset' ).hide();
            $( config.elements.progressList ).hide();
            $( config.elements.submitButton ).hide();
            $( config.elements.nextButton ).hide();
            $( config.elements.previousButton ).hide();

            $( '#romacId' ).text( data );
            $( config.elements.confirmationElement ).show();
        }

        function areConditionsAccepted() {
            return $( config.elements.conditionsAcceptedInput ).val() == 'true';
        }

        function isFirstFieldset() {
            return activeFieldsetIndex == 0;
        }

        function isLastFieldset() {
            return !(activeFieldsetIndex < $( 'fieldset' ).size()-1);
        }

        function validateCurrentFieldset() {
            return validateFieldset( activeFieldsetIndex );
        }

        return {
            init: init,
            config: config
        }
    }();

    /**
     * Bootstrap document ready.
     */
    $( document ).ready( function() {
        referralForm.init();
    } );
})( jQuery );