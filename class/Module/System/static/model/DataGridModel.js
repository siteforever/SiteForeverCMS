/**
 * Model for item in admin module
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("system/model/DataGridModel", [
    "backbone"
], function (Backbone) {
    return Backbone.Model.extend({
        idAttribute: "id",
        url: function() {
            var base = this.urlRoot || (this.collection && this.collection.baseUrl);
            if (this.isNew()) return base;
            return base + '?id=' +encodeURIComponent(this.id);
        }
    });
});
