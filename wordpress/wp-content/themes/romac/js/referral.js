// be safe - map $ to jQuery in a anonymous function
(function($) {
    // bootstrap on document ready
    $( document ).ready(function() {
        var preamable =      $( '#referral-preamble' );
        var progress =       $( '#referral-progress ul' );
        var progress_items = null; // instantiated by init_steps()
        var fieldsets =      $( '#referral-form fieldset' );
        var submit_btn =     $( '#referral-form input[type="submit"]' );
        var begin_btn  =     $( '#referral-begin' );
        var prev_btn =       $( '#referral-prev' );
        var next_btn =       $( '#referral-next' );

        var has_began = false;
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
            }

            // if the first panel
            if (cur_fieldset == 0) {
                prev_btn.attr( 'disabled', '' );
            }
        }

        function init_steps() {
            var i = 1;
            fieldsets.each(function() {
                progress.append( '<li>' + i++ + '</li>');
            });
            progress_items = $( '#referral-progress ul li' );
        }

        ping();
        init_steps();

        begin_btn.click(function() {
            has_began = true;
            ping();
        });

        next_btn.click(function() {
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

        /**
         * Reads a file typed input and assigns the encoded image src to the #patient-photo element.
         *
         * @param input The source input[type=file]
         */
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#patient-photo').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $( "#photo-photo-input" ).change(function() {
            readURL(this);
        });

        var select2options = { maximumSelectionLength: 2 };
        $( "#patient-languages-spoken" ).select2( select2options );
        $( "#patient-mother-languages-spoken" ).select2( select2options );
        $( "#patient-father-languages-spoken" ).select2( select2options );

        function showOptionalGroup(input, groupId) {
            if ( $(input).val() == 'Yes' ) {
                $( groupId ).show( 'slow' );
            } else {
                $( groupId ).hide( 'slow' );
            }
        }
        $( "#patient-mother-has-yes" ).change(function() {
            showOptionalGroup( this, '#patient-mother-optional-group' );
        });
        $( "#patient-mother-has-no" ).change(function() {
            showOptionalGroup( this, '#patient-mother-optional-group' );
        });
        $( "#patient-father-has-yes" ).change(function() {
            showOptionalGroup( this, '#patient-father-optional-group' );
        });
        $( "#patient-father-has-no" ).change(function() {
            showOptionalGroup( this, '#patient-father-optional-group' );
        });
    });
})( jQuery );
