// User edit form customizations
require(['jquery'], function($) {
    var $elem = $('#page-user-editadvanced #fitem_id_firstname'); // Admin user profile form
    if (!$elem.length) {
        $elem = $('#page-user-edit #fitem_id_firstname'); // User profile form
    }
    if ($elem.length) {
        $elem.prepend('<div class="col-md-3"></div><div class="col-md-9">'
            + '<a href="https://it.ucsf.edu/how-to/how-update-your-name" target="_blank">How to update name information.</a>'
            + '</div>');
    }
});
