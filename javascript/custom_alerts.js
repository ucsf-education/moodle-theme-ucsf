require(['jquery'], function($) {
    $('.ucsfx-alert').on('closed.bs.alert', function(event) {
        var $elem = $(event.target);
        var targetUrl = $elem.attr('data-ucsfx-target-url');
        var alertId = $elem.attr('data-ucsfx-alert-id');
        $.get(targetUrl, {alert: alertId});
    });
});
