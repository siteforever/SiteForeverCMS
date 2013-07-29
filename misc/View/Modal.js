/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define('View/Modal',[
    "jquery",
    "backbone",
    "jquery/jquery.form"
], function($, Backbone){
    return Backbone.View.extend({
        tagName: 'div',
        className: 'modal hide',

        $overlay: null,

        title: 'Modal',

        content: function() {
            return _.template($('#tplAdminItemEdit').html(), this.model.toJSON());
        },

        codyData: {},

        moving: false,
        oldLeft: 0,
        oldTop: 0,

        posLeft: 0,
        posTop: 0,

        width: 800,
        height: 300,

        tplTitle: null,
        tplBody: null,
        tplFooter: null,

        initialize: function()
        {
            console.log(this.options);
            $('body').mousemove($.proxy(this.onMove, this)).mouseup($.proxy(this.onMoveEnd, this));
        },

        events: {
            'keyup :input': 'onType',
            'click :checkbox': 'onCheck',
            'mousedown div.modal-header': 'onMoveStart'
        },

        buttons: [
        ],

        onMoveStart: function(event) {
            this.moving = true;
            this.oldLeft = event.pageX;
            this.oldTop = event.pageY;
        },

        onMoveEnd: function(event) {
            this.moving = false;
        },

        onMove: function(event) {
            if (!this.moving) return false;
            this.posLeft += (event.pageX - this.oldLeft);
            this.posTop += (event.pageY - this.oldTop);
            this.oldLeft = event.pageX;
            this.oldTop = event.pageY;
            this.$el.css({"margin-top": this.posTop, "margin-left": this.posLeft});
            return true;
        },

        onType: function(event) {
            var $node = $(event.target),
                $control = $node.parents('.control-group');

            this.model.set($control.data('field-name'), $node.val());
        },

        onCheck: function(event) {
            var $node = $(event.target),
                $control = $node.parents('.control-group');
            this.model.set($control.data('field-name'), $node.val() && $node.attr('checked') ? 1 : 0);
        },

        show: function() {
            this.$el.removeClass('hide');
            if (this.$overlay) {
                this.$overlay.removeClass('hide');
            } else if (($overlay = $('.modal-backdrop')).length) {
                this.$overlay = $overlay.first().removeClass('hide');
            } else {
                this.$overlay = $(this.make("div", {class:"modal-backdrop fade in"})).appendTo('body');
            }

            return false;
        },


        hide: function() {
            this.$el.addClass('hide');
            if (this.$overlay) {
                this.$overlay.addClass('hide');
            }
            return false;
        },



        moveToCenter: function() {
            this.$el.find('div.modal-body').css('min-height', this.height);
            this.posLeft = - Math.round(this.width / 2);
            this.posTop = - Math.round(Math.max(this.$el.height(), this.height) / 2);
            this.$el.css({
                position: "fixed",
                top: '50%',
                left: '50%',
                width: this.width,
                "margin-left": this.posLeft,
                "margin-top": this.posTop
            });
            return this;
        },

        render: function(options) {
            if (!$('body').find(this.el).length) {
                this.$el.appendTo('body');
            }
            this.$el.html([
                this._renderTitle(this.title),
                this._renderBody((options && options.content) || this.content()),
                this._renderFooter()
            ].join(""));
            if (!(this.posLeft && this.posTop)) {
                this.moveToCenter();
            }
            this.show();
            this.codyData = this.model.toJSON();

            return this;
        },


        _renderTitle: function(title) {
            return _.template([
                '<div class="modal-header">',
                '<button type="button" class="close btn-close">&times;</button>',
                    '<h3><%- title %></h3>',
                '</div>'
            ].join(""), {title: title});
        },
        _renderBody: function(content) {
            return _.template('<div class="modal-body"><%= content %></div>', {content: content});
        },
        _renderFooter: function() {
            return [
                '<div class="modal-footer">',
                _.reduce(this.buttons, function(memo, button){
                    return memo += _.template('<button class="<%= classes %>"><%= text %></button>', button);
                }, "", this),
                '</div>'
            ].join("");
        }
    });
});
