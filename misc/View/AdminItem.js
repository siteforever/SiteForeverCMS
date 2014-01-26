/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("View/AdminItem", [
    "jquery",
    "backbone"
], function ($, Backbone) {
    return Backbone.View.extend({
        tagName: "tr",
        className : "b-admin-item",
        events: {
            'click td a.edit': "onEdit",
            'click td a.delete': "onDelete"
        },

        _modalView: null,

        winManager: null,

        initialize: function(options) {
            this.winManager = options.winManager || null;
            this.model.on('change', this.render, this);
        },

        modalView: function() {
            if (this.winManager && !this._modalView) {
                this._modalView = this.winManager.create({model: this.model});
            }
            return this._modalView;
        },

        render: function() {
            this.$el.html(_.template(this.tplAdminItem, this.model.toJSON()));
            return this;
        },

        onEdit: function(event) {
            event.stopPropagation();

            this.modalView().render();
//            console.log(this.model.url());
            $.get(this.model.url()).done($.proxy(function(response){
                this.modalView().render({content: response});
            }, this));

            return false;
        },

        onDelete: function(event) {
            event.stopPropagation();
            if (confirm('Would you like to delete?')) {
                this.model.destroy();
            }
            return false;
        }
    });
});
