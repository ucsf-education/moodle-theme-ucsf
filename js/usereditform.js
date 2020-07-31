// User edit form customizations
require(['jquery'], function($) {
    var $elem = $('#page-admin-user-editadvanced #fitem_id_firstname');
    if ($elem.length) {
        $elem.prepend('<div class="col-md-3"></div><div class="col-md-9">Lorem ipsum dolor sit amet... <a href="#">Link to somewhere</a>.</div>');
    }
});
