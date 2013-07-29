/**
 *
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
        $pagination: null,
        $table: null,
        pages: 1,
        page: 1,
        orderDirs: {},
        order: false,
        dir: 'asc',

        filter: {},

        loading: false,
        timeout: null,

        collection: new ItemCollection,

        baseUrl: '',

        _modalView: null,

        /**
         * Events
         */
        events: {
            'click th>a': "onSort",
            'click div.pagination a' : "onPage",
            'click .btn-add': 'onAdd',
            'keyup .sfcms-admin-dataset-fiter input': "onFilter",
            'keypress .sfcms-admin-dataset-fiter input': "onFilterPress",
            'click .btn-refresh' : "loadData"
        },

        /**
         * Init
         * @returns {*}
         */
        initialize: function() {
            this.$pagination = this.$el.find('.pagination');
            this.$table = this.$el.find('.table');
            this.page  = this.$pagination.data('page');
            this.tplAdminItem = this.options.tplAdminItem;
            this.tplAdminPagingItem = this.options.tplAdminPagingItem;


            if (!this.$el.data('url')) {
                throw new Error('A "url" attribute must be specified');
            }
            this.baseUrl = this.collection.url = this.$el.data('url');

            this.$el.find('.sfcms-admin-dataset-fiter :input').each($.proxy(function(i, node){
                var $node = $(node),
                    val = $.trim($node.val());
                if (val && val.length > 1) {
                    this.filter[$node.data('col')] = val;
                }
            }, this));

            // Rendering after appended new or remove item
            this.collection.on('add remove', this.loadData, this);
            if (this.options.dispatcher) {
                this.options.dispatcher.on('admin.model.save', function(model){
                    model && model.isNew && this.collection.add(model);
                }, this);
            }
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
                    winManager: this.options.winManager
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

        /**
         * Loading data from server
         * @returns {*}
         */
        loadData: function(){
            this.loading = true;
            this.$el.block({message: 'Loading...'});
            this.collection.url = this.baseUrl;

            this.collection.url += '&page=' + this.page;
            if (this.order) {
                this.collection.url += '&o=' + this.order + '&dir=' + this.dir;
            }
            if (this.filter) {
                var key;
                for (key in this.filter) {
                    if (this.filter[key].length >= 2) {
                        this.collection.url += '&filter[' + key + ']=' + this.filter[key];
                    }
                }
            }

            return this.collection.fetch()
                .done($.proxy(this.render, this))
                .done($.proxy(this.$el.unblock, this.$el))
                .done($.proxy(function(){
                    this.loading = false;
                    this.collection.url = this.baseUrl;
                }, this))
                .fail(function(collection, Response){
                    $alert(Response.responseText, 3000);
                }).promise();
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
            var $node = $(event.target),
                col   = $node.data('ord'),
                dir = this.orderDirs[col] ? this.orderDirs[col] : 'desc';

            if (this.order == col) {
                dir = ('desc' == dir) ? 'asc' : 'desc';
            } else {
                this.order = col;
            }
            this.orderDirs[col] = dir;
            this.dir = dir;
            this.loadData();
            return false;
        },

        /**
         * Press on button for switch page
         * @param event
         * @returns {boolean}
         */
        onPage: function(event) {
            var $target = $(event.target);
            if (!$target.parent().hasClass('active')) {
                this.page = $target.data('page');
                this.loadData();
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
            if (event.keyCode < 32 && event.keyCode != 8) {
                return false;
            }

            var $target = $(event.target),
                col = $target.data('col'),
                val = $.trim($target.val());

            if (val == this.filter[col] || !(val || this.filter[col])) {
                return false;
            }

            if (val) {
                this.filter[col] = val;
            } else {
                delete this.filter[col];
            }

            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            if (val.length > 0 && val.length < 2) {
                return false;
            }
            this.timeout = setTimeout($.proxy(this.loadData, this), 500);
            return true;
        },

        /**
         * Press button for Add new item
         * @param event
         */
        onAdd: function(event) {
            event.stopPropagation();
            var item = new Item(),
                win = this.options.winManager.create({model: item});
            win.render();

            item.urlRoot = $(event.target).data('href');

            $.post(item.url()).done($.proxy(function(response){
                win.render({content: response});
            }, this));
        }
    });
});


