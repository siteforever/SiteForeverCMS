/**
 * Backbone+Bootstrap DataGrig
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

define("View/DataGrid", [
    "jquery",
    "backbone",
    "module/alert",
    "Model/AdminItem",
    "View/AdminItem",
    "Model/AdminItemCollection",
    "jquery/jquery.blockUI"
], function($, Backbone, $alert, Item, ItemView, ItemCollection){
    return Backbone.View.extend({
        $table: null,
        $pagination: null,

        loading: false,
        timeout: null,

        collection: new ItemCollection,

        _modalView: null,

        dispatcher: null,
        winManager: null,

        /**
         * Events
         */
        events: {
            'click th>a': "onSort",
            'click div.pagination a' : "onPage",
            'click .btn-add': 'onAdd',
            'keyup .sfcms-admin-dataset-fiter input': "onFilter",
            'keypress .sfcms-admin-dataset-fiter input': "onFilterPress",
            'click .btn-refresh' : "onRefresh"
        },

        /**
         * Init
         * @returns {*}
         */
        initialize: function(options) {
            _.bindAll(this, 'render', 'onRefresh', 'modalView', 'onSort', 'onPage', 'onAdd', 'onFilter', 'onFilterPress');

            this.$pagination = this.$el.find('.pagination');
            this.$table = this.$el.find('.table');

            this.tplAdminItem = options.tplAdminItem;
            this.tplAdminPagingItem = options.tplAdminPagingItem;
            this.dispatcher = options.dispatcher;
            this.winManager = options.winManager;


            if (!this.$el.data('url')) {
                throw new Error('A "url" attribute must be specified');
            }

            this.collection.baseUrl = this.$el.data('url');
            this.collection.page = this.$pagination.data('page');

            this.$el.find('.sfcms-admin-dataset-fiter :input').each($.proxy(function(i, node){
                var $node = $(node),
                    val = $.trim($node.val());
                if (val && val.length > 1) {
                    this.filter[$node.data('col')] = val;
                }
            }, this));

            if (this.dispatcher) {
                this.dispatcher.on('admin.model.save', function(model){
                    model && model.isNew && this.collection.add(model);
                }, this);
            }

            this.collection.on('sync add remove', this.render);
            this.collection.on('request', function(){
                this.$el.block({message: 'Loading...'});
            }, this);
            this.collection.on('sync', function(){
                this.$el.unblock();
            }, this);

            this.collection.fetch();

            return this;
        },

        /**
         * Render for view
         * @returns {*}
         */
        render: function() {
            var $rows = this.$table.find('tbody');
            $rows.find('*').remove();
            if (!this.collection.length) {
                this.pages = 0;
            }
            this.collection.each(function(objItem, i){
                0 == i && (this.pages = objItem.get('_p'));
                var view = new ItemView({
                    model : objItem,
                    winManager: this.winManager
                });
                view.tplAdminItem = this.tplAdminItem;
                view.render();
                $rows.append(view.$el);
            }, this);

            this.$table.find('a[data-ord]').each(function(){
                $(this).removeClass();
            });
            this.$table.find('a[data-ord='+this.order+']').addClass('order-' + this.dir);

            var i,
                $pageContainer = this.$pagination.find('ul');

            $pageContainer.find('li:gt(0)').remove();
            if (this.pages > 1) {
                for (i = 1; i <= this.pages; i++) {
                    $pageContainer.append(_.template(this.tplAdminPagingItem, {
                        number: i,
                        url: this.collection.url,
                        attrClass: (this.page == i ? 'active' : '')
                    }));
                }
            } else {
                this.$el.find('.pagination').hide();
            }
            return this;
        },

        onRefresh: function() {
            this.collection.fetch();
        },

        /**
         * Return view object for Modal window
         * @returns {null}
         */
        modalView: function() {
            if (!this._modalView) {
                this._modalView = (new AdminModal({model: this.model}));
            }
            return this._modalView;
        },

        /**
         * Click on sort switch off
         * @param event
         * @returns {boolean}
         */
        onSort: function(event) {
            return this.collection.onSort(event);
        },

        /**
         *
         * Press on button for switch page
         * @param event
         * @returns {boolean}
         */
        onPage: function(event) {
            var $target = $(event.target);
            if (!$target.parent().hasClass('active')) {
                this.collection.page = $target.data('page');
                this.collection.fetch();
            }
            return false;
        },

        /**
         * Key press in filter input
         * @param e
         * @returns {boolean}
         */
        onFilterPress: function(e) {
            if (e.ctrlKey || e.altKey || e.metaKey) return false;
            var symb = String.fromCharCode(e.keyCode);
            if (!symb) return false; // спец. символ - не обрабатываем
            return !this.loading;
        },

        /**
         * Key up in filter input
         * @param event
         * @returns {boolean}
         */
        onFilter: function(event) {
            return this.collection.onFilter(event);
        },

        /**
         * Press button for Add new item
         * @param event
         */
        onAdd: function(event) {
            event.stopPropagation();
            var item = new Item(),
                win = this.winManager.create({model: item});
            win.render();

            item.urlRoot = $(event.target).data('href');

            $.post(item.url()).done($.proxy(function(response){
                win.render({content: response});
            }, this));
        }
    });
});


