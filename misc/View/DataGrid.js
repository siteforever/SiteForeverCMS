/**
 * Backbone+Bootstrap DataGrig
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

define("View/DataGrid", [
    "jquery",
    "backbone",
    "i18n",
    "module/alert",
    "Model/DataGridModel",
    "Model/DataGridCollection",
    "View/DataGridModal",
    "jquery/jquery.blockUI"
], function ($, Backbone, i18n, $alert, DataGridModel, DataGridCollection, DataGridModal) {

    var DataGridItemView = Backbone.View.extend({
        tagName: "tr",
        className: "b-admin-item",
        events: {
            'click td a.edit': "onEdit",
            'click td a.delete': "onDelete"
        },

        modalView: null,

        initialize: function () {
            this.model.on('change', this.render, this);
        },

        render: function () {
            this.$el.html(_.template(this.tplAdminItem, this.model.toJSON()));
            return this;
        },

        onEdit: function (event) {
            event.stopPropagation();

            this.modalView.model = this.model;
            this.modalView.render({title: i18n('Edit')});
            $.get(this.model.url()).done($.proxy(function (response) {
                this.modalView.render({content: response});
            }, this));

            return false;
        },

        onDelete: function (event) {
            event.stopPropagation();
            if (confirm(i18n('Would you like to delete?'))) {
//                var c = this.model.collection;
                this.model.destroy();
//                c.fetch();
            }
            return false;
        }
    });

    return Backbone.View.extend({
        $table: null,
        $pagination: null,

        loading: false,
        timeout: null,

        collection: new DataGridCollection,

        modalView: null,

        dispatcher: null,
//        winManager: null,

        /**
         * Events
         */
        events: {
            'click th>a': "onSort",
            'click div.pagination a': "onPage",
            'click .btn-add': 'onAdd',
            'keyup .sfcms-admin-dataset-fiter input': "onFilter",
            'keypress .sfcms-admin-dataset-fiter input': "onFilterPress",
            'click .btn-refresh': "onRefresh"
        },

        /**
         * Init
         * @returns {*}
         */
        initialize: function (options) {
            _.bindAll(this, 'render', 'onRefresh', 'onSort', 'onPage', 'onAdd', 'onFilter', 'onFilterPress');

            this.$pagination = this.$el.find('.pagination');
            this.$table = this.$el.find('.table');

            this.tplAdminItem = options.tplAdminItem;
            this.tplAdminPagingItem = options.tplAdminPagingItem;
            this.dispatcher = options.dispatcher;
//            this.winManager = options.winManager;

            this.modalView = new DataGridModal({dispatcher: options.dispatcher});

            if (!this.$el.data('url')) {
                throw new Error('A "url" attribute must be specified');
            }

            this.collection.baseUrl = this.$el.data('url');
            this.collection.page = this.$pagination.data('page');

            this.$el.find('.sfcms-admin-dataset-fiter :input').each($.proxy(function (i, node) {
                var $node = $(node),
                    val = $.trim($node.val());
                if (val && val.length > 1) {
                    this.filter[$node.data('col')] = val;
                }
            }, this));

            if (this.dispatcher) {
                // triggered from DataGridModal::onSaveSuccess()
                this.dispatcher.on('admin.model.save', function (model) {
                    model && model.isNew && this.collection.fetch();
                }, this);
            }


            this.collection
                .on('request', function (model, xhr, options) {
                    this.collection.off('add');
                    this.$el.block({message: i18n('Loading...')});
                }, this)
                .on('destroy', this.render, this)
                .on('sync', this.render, this)
                .on('sync', function () {
                    this.collection.on('add', this.render);
                }, this);

            this.collection.fetch();

            return this;
        },

        /**
         * Render for view
         * @returns {*}
         */
        render: function () {
            var $rows = this.$table.find('tbody');
            $rows.find('*').remove();
            if (!this.collection.length) {
                this.collection.pages = 0;
            }
            this.collection.each(function (objItem, i) {
                if (0 == i) this.collection.pages = objItem.get('_p');
                var view = new DataGridItemView({model: objItem});
                view.modalView = this.modalView;
                view.tplAdminItem = this.tplAdminItem;
                view.render();
                $rows.append(view.$el);
            }, this);

            this.$table.find('a[data-ord]').each(function () {
                $(this).removeClass();
            });
            this.$table.find('a[data-ord=' + this.order + ']').addClass('order-' + this.dir);

            var i,
                $pageContainer = this.$pagination.find('ul'),
                $pagination = this.$el.find('.pagination');

            $pageContainer.find('li:gt(0)').remove();
            if (this.collection.pages > 1) {
                for (i = 1; i <= this.collection.pages; i++) {
                    $pageContainer.append(_.template(this.tplAdminPagingItem, {
                        number: i,
                        url: this.collection.url(),
                        attrClass: (this.collection.page == i ? 'active' : '')
                    }));
                }
                $pagination.show();
            } else {
                $pagination.hide();
            }
            this.$el.unblock();

            return this;
        },

        onRefresh: function () {
            this.collection.fetch();
        },

        /**
         * Click on sort switch off
         * @param event
         * @returns {boolean}
         */
        onSort: function (event) {
            return this.collection.onSort(event);
        },

        /**
         *
         * Press on button for switch page
         * @param event
         * @returns {boolean}
         */
        onPage: function (event) {
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
        onFilterPress: function (e) {
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
        onFilter: function (event) {
            return this.collection.onFilter(event);
        },

        /**
         * Press button for Add new item
         * @param event
         */
        onAdd: function (event) {
            event.stopPropagation();
            var item = new DataGridModel();

            item.urlRoot = $(event.target).data('href');

            this.modalView.model = item;
            this.modalView.title = i18n('Create');
            this.modalView.render();

            $.post(item.url()).done($.proxy(function (response) {
                this.modalView.render({content: response});
            }, this));
        }
    });
});


