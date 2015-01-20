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

    // pre-conditions passed: shift the line
    var position = jQuery(s).position();
    var left = position.left;
    var paddingRight = parseInt(jQuery(s).css("padding-right"));
    var width = jQuery(s).outerWidth() - paddingRight;
    bb.css("left", left).css("width", width);
    return true;
}

jQuery(document).ready(
    function() {
        var bottomBarElement = '<div class="avada-row"><div class="bottom-bar"></div></div>';
        jQuery("#small-nav").append(bottomBarElement);
        if (shiftLine('.current_page_item') == false) {
            shiftLine('.current-page-ancestor');
        }

        jQuery("#nav ul > li").hover(
            function() {
                shiftLine(this);
            }, function() {
                if (shiftLine('.current_page_item') == false) {
                    shiftLine('.current-page-ancestor');
                }
            }
        );
    }
);
