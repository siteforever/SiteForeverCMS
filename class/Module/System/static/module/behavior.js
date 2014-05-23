/**
 * Плагин поведений
 * @author: keltanas
 * @link http://siteforever.ru
 */

define("system/module/behavior", [
    "jquery"
], function ($) {
    return {
        apply: function (object) {
            if (object.behavior && typeof object.behavior == "object") {
                $.each(object.behavior, function (selector) {
                    if (typeof this == "object") {
                        $.each(this, function (eventType, callBack) {
                            $(document).on(eventType, selector, [callBack, object], function(event) {
                                return event.data[0].call(event.data[1], event, this);
                            });
                        });
                        return true;
                    }
                    console.error(typeof this + ' not supported');
                    return false;
                });
            }
        }
    }
});
