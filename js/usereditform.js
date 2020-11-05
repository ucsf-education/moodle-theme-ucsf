// User edit form customizations
require(['jquery'], function($) {
    var $elem = $('#page-admin-user-editadvanced #fitem_id_firstname');
    if ($elem.length) {
        $elem.prepend('<div class="col-md-3"></div><div class="col-md-9"><a href="https://it.ucsf.edu/how-to/how-update-your-name" target="_blank">How to update name information.</a></div>');
    }
});
