/**
 * Collection for admin item model
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("Model/AdminItemCollection", [
    "backbone",
    "Model/AdminItem"
], function(Backbone, AdminItemModel){
    return Backbone.Collection.extend({
        model: AdminItemModel
    });
});
