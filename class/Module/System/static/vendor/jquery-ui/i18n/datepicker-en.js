/* English (UTF-8) initialisation for the jQuery UI date picker plugin. */
define('datepicker_i18n', [
    'jquery',
    'jquery-ui'
], function ($) {
     $.extend($.datepicker.regional['en'], {
        dateFormat: 'dd.mm.yyyy',
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true
    });
    $.fn.datepicker.setDefaults($.fn.datepicker.regional['en']);

    return $.fn.datepicker.regional['en'];
});
