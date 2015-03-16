var formSubmitting = false;
var has_began = false;
var setFormSubmitting = function() { formSubmitting = true; };

window.onload = function() {
    window.addEventListener("beforeunload", function (e) {
        var confirmationMessage = 'You have started a referral form submission. ';
        confirmationMessage += 'If you leave before saving, your changes will be lost.';

        if (formSubmitting || !has_began) {
            return undefined;
        }

        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
};

// be safe - map $ to jQuery in a anonymous function
(function($) {
    // bootstrap on document ready
    $( document ).ready(function() {
        var refer_panel =    2;
        var preamable =      $( '#referral-preamble' );
        var progress =       $( '#referral-progress ul' );
        var progress_items = null; // instantiated by init_steps()
        var fieldsets =      $( '#referral-form fieldset' );
        var submit_btn =     $( '#referral-form input[type="submit"]' );
        var begin_btn  =     $( '#referral-begin' );
        var prev_btn =       $( '#referral-prev' );
        var next_btn =       $( '#referral-next' );
        var accompany =      $( '#patient-accompaniment' );
        var cur_fieldset = 0;

        function ping() {
            if ( !has_began ) {
                fieldsets.hide();
                progress.hide();
                submit_btn.hide();
                next_btn.hide();
                prev_btn.hide();
                return;
            }

            // defaults for began referral form
            preamable.hide();
            begin_btn.hide();
            progress.show();

            submit_btn.show();
            submit_btn.attr( 'disabled', '' );

            // reset next button
            next_btn.show();
            next_btn.removeAttr( 'disabled' );

            // reset previous button
            prev_btn.show();
            prev_btn.removeAttr( 'disabled' );

            // reset fieldsets - show active
            fieldsets.hide();
            fieldsets.eq( cur_fieldset ).show();

            // reset progress - append active class
            progress_items.each(function() {
                $( this ).removeClass();
            });
            progress_items.eq( cur_fieldset ).addClass( 'active' );

            // if the last panel
            if (cur_fieldset == fieldsets.size()-1) {
                next_btn.attr( 'disabled', '' );
                submit_btn.removeAttr( 'disabled' );

                // populate the confirmation screen
                $("#referral-form input, #referral-form select, #referral-form textarea").each(function(index, element) {
                    var name = $(element).attr('name');
                    var confirmId = '#' + name + '-c';
                    var confirmElem = $(confirmId);
                    // add guard in-case input does not have a preview
                    if (confirmElem.length) {
                        // its a <select> option
                        if ($(element).is('select')) {
                            confirmElem.text($('select[name="' + name + '"] option:selected').text());
                        }
                        // everything else
                        else {
                            confirmElem.text($(element).val());
                        }
                    }
                });
            }

            // if the first panel
            if (cur_fieldset == 0) {
                prev_btn.attr( 'disabled', '' );
            }

            if (cur_fieldset == refer_panel) {
                var hasMother =    $( 'input[name="patient-has-mother"]:checked').val();
                var hasMotherOpt = $( '#patient-accompaniment option[value="mother"]' );
                var hasFather =    $( 'input[name="patient-has-father"]:checked').val();
                var hasFatherOpt = $( '#patient-accompaniment option[value="father"]' );

                if ( hasMother == "Yes" && !hasMotherOpt.length ) {
                    accompany.append( $('<option>', {
                        'value': 'mother',
                        'text': 'Mother',
                        'selected': ''
                    }));
                }
                if ( hasMother == "No" && hasMotherOpt.length) {
                    hasMotherOpt.remove();
                }

                if ( hasFather == "Yes" && !hasFatherOpt.length ) {
                    accompany.append( $('<option>', {
                        'value': 'father',
                        'text': 'Father'
                    }));
                }
                if ( hasFather == "No" && hasFatherOpt.length ) {
                    hasFatherOpt.remove();
                }

                accompanyChange();
            }
        }

        function init_steps() {
            var i = 1;
            fieldsets.each(function() {
                progress.append( '<li>' + i++ + '</li>');
            });
            progress_items = $( '#referral-progress ul li' );
        }

        begin_btn.click(function() {
            has_began = true;
            ping();
        });

        next_btn.click(function() {
            if ( !validateFieldset( cur_fieldset ) ) return;

            if (cur_fieldset < fieldsets.size()-1) {
                cur_fieldset++;
                ping();
            }
        });

        prev_btn.click(function() {
            if (cur_fieldset > 0) {
                cur_fieldset--;
                ping();
            }
        });

        function accompanyChange() {
            var val = accompany.val();
            if ( val == "other" ) {
                $( "#patient-accompaniment-optional-group" ).show();
            } else {
                $( "#patient-accompaniment-optional-group" ).hide();
            }
        }
        accompany.change( accompanyChange );

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

        $( "#patient-photo-1-input" ).change(function() {
            readURL(this, '#patient-photo-1');
        });
        $( "#patient-photo-2-input" ).change(function() {
            readURL(this, '#patient-photo-2');
        });
        $( "#patient-photo-3-input" ).change(function() {
            readURL(this, '#patient-photo-3');
        });

        /**
         * Adds a clicks handlers to the add photo button.
         *
         * It will add a new line for a photo as long as there are no other empty inputs.
         */
        $( "#add-photo" ).click(function() {
            // count the number of photos
            var currentInputs = $( "#patient-photos input" );
            if (currentInputs.last().val() != '') {
                var i = currentInputs.length;
                $( "#patient-photos" ).append(
                    '<div class="form-control"><input type="file" name="patient-photo-' + i + '-input"></div>');
            }
        });

        /**
         * Adds a clicks handlers to the add document button.
         *
         * It will add a new line for a document as long as there are no other empty inputs.
         */
        $( "#add-document" ).click(function() {
            // count the number of photos
            var currentInputs = $( "#patient-documents input" );
            if ( currentInputs.last().val() != '' ) {
                var i = currentInputs.length;
                $( "#patient-documents" ).append(
                    '<div class="form-control"><input type="file" name="patient-document-' + i + '-input"></div>');
            }
        });

        //
        // Instantiate special form controls - select2, datepicker etc
        //
        var select2options = { maximumSelectionLength: 2 };
        $( "#patient-languages-spoken" ).select2( select2options );
        $( "#patient-mother-languages-spoken" ).select2( select2options );
        $( "#patient-father-languages-spoken" ).select2( select2options );
        $( "#patient-accompaniment-languages-spoken" ).select2( select2options );

        //$( "#patient-country-of-origin" ).select2();
        //$( "#patient-nationality" ).select2();
        //$( "#patient-religion" ).select2();

        //var datepickerOptions = { };
       // $( "#patient-dob" ).datepicker( datepickerOptions );

        function showOptionalGroup(input, groupId) {
            if ( $(input).val() == 'Yes' ) {
                $( groupId ).show( 'slow' );
            } else {
                $( groupId ).hide( 'slow' );
            }
        }
        $( "#patient-has-mother-yes" ).change(function() {
            showOptionalGroup( this, '#patient-mother-optional-group' );
        });
        $( "#patient-has-mother-no" ).change(function() {
            showOptionalGroup( this, '#patient-mother-optional-group' );
        });
        $( "#patient-has-father-yes" ).change(function() {
            showOptionalGroup( this, '#patient-father-optional-group' );
        });
        $( "#patient-has-father-no" ).change(function() {
            showOptionalGroup( this, '#patient-father-optional-group' );
        });

        function validateFieldset( groupId ) {
            function validateField(fieldId) {
                if ( !referralForm.element( fieldId ) ) {
                    isValid = false;
                }
            }

            var referralForm = $( '#referral-form' ).validate({
                //messages: {
                //    'patient-fname': 'The patients first name is required.',
                //    'patient-lname': 'The patients last name is required.',
                //    'patient-sex': 'The patients gender is required.'
                //}
            });
            var isValid = true;

            switch ( groupId ) {
                case 0: // patient details
                    validateField( '#patient-fname' );
                    validateField( '#patient-lname' );
                    validateField( '#patient-dob' );
                    validateField( '#patient-sex' );
                    validateField( '#patient-height' );
                    validateField( '#patient-weight' );
                    validateField( '#patient-address' );
                    validateField( '#patient-country-of-origin' );
                    validateField( '#patient-nationality' );
                    validateField( '#patient-religion' );
                    validateField( '#patient-languages-spoken' );
                    validateField( 'input[name="patient-understand-english"]' );
                    break;
            }

            return isValid;
        }

        ping();
        init_steps();
    });
})( jQuery );