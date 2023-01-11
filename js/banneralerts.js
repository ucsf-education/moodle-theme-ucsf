require(['jquery'], function($) {
    $('.ucsf-banneralerts-alert').on('closed.bs.alert', function(event) {
        var $elem = $(event.target);
        var url = $elem.data('ucsf-banneralert-dismiss-callback-url');
        var id = $elem.data('ucsf-banneralerts-alert-id');
        $.get(url, {id: id});
    });
});