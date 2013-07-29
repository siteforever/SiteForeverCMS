/**
 * Model for item in admin module
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("Model/AdminItem", [
    "backbone"
], function (Backbone) {
    return Backbone.Model.extend({
        idAttribute: "id",
        url: function() {
            var base = this.urlRoot || (this.collection && this.collection.url);
            if (this.isNew()) return base;
            return base + '&id=' +encodeURIComponent(this.id);
        }
    });
});
