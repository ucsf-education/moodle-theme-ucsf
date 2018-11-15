/**
 * Instantiates new datepicker instances.
 */
require(['jquery', 'theme_ucsf/pikaday'], function($, Pikaday) {
  $('.datepicker').each(function(i, elem) {
    new Pikaday({
      field: elem,
      toString: function(date) {
        // @link https://stackoverflow.com/a/15764763
        var year = date.getFullYear();
        var month = (1 + date.getMonth()).toString();
        month = month.length > 1 ? month : '0' + month;
        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        return month + '/' + day + '/' + year;
      }
    });
  });
});
