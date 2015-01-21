/**
 * Shifts the left position of the .bottom-bar element relative to
 * the selector (s).
 */
function shiftLine(s) {
    if (typeof s === 'undefined') return false; // no param

    // if selector does not match, hide the bottom bar
    var bb = jQuery(".bottom-bar");
    if (!jQuery(s).length) {
        bb.css("left", "0").css("width", "0");
        return false;
    }

    // ignore submenu items - we only want the red line positioned on parent elements
    if (jQuery(s).hasClass("fusion-dropdown-submenu")) {
        return false;
    }

    // pre-conditions passed: shift the line
    var position = jQuery(s).position();
    var left = position.left;
    var paddingRight = parseInt(jQuery(s).css("padding-right"));
    var width = jQuery(s).outerWidth() - paddingRight;
    bb.css("left", left).css("width", width);
    return true;
}

function alignCoverDiv() {
    var margin = 30;
    var headerHeight = jQuery(".header-wrapper").outerHeight();
    var footerHeight = jQuery(".footer-area").outerHeight() + jQuery("#footer").outerHeight();
    var documentHeight = jQuery(window).height();
    var coverHeight = documentHeight - headerHeight - footerHeight - (margin * 3);

    jQuery(".iqo-cover").css('height', coverHeight + 'px');
}

jQuery(document).ready(
    function() {
        var bottomBarElement = '<div class="avada-row"><div class="bottom-bar"></div></div>';

        jQuery("#small-nav").append(bottomBarElement);
        if (shiftLine('.current-menu-parent') == false) {
            shiftLine('.current-menu-item');
        }

        jQuery("#nav ul > li").hover(
            function() {
                shiftLine(this);
            }, function() {
                if (shiftLine('.current-menu-parent') == false) {
                    shiftLine('.current-menu-item');
                }
            }
        );

        // if the front page is being viewed (as determined by the iqo-cover class,
        // then attach a resize function to the iqo-cover element that dynamically
        // adjust the height of this element.
        if (jQuery('.iqo-cover').length) {
            // call once on load
            alignCoverDiv();

            // add the resize handler
            jQuery(window).resize(alignCoverDiv);
        }

    }
);
