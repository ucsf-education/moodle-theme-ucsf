require(['jquery'], function($) {
    $('.ucsf-alert').on('closed.bs.alert', function(event) {
        var $elem = $(event.target);
        var targetUrl = $elem.attr('data-ucsf-target-url');
        var alertId = $elem.attr('data-ucsf-alert-id');
        $.get(targetUrl, {alert: alertId});
    });
});
