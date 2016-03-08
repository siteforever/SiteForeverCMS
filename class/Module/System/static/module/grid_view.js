/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("system/module/grid_view", [
    "jquery",
    "underscore",
    "backbone",
    "jqgrid/i18n/grid.locale-ru",
    "jqgrid/grid.filter",
    "jqgrid/grid.formedit"
], function ($, _, Backbone) {

    var tableConfig = {
        url: null,
        caption: null,
        mtype: "GET",
        datatype: "json",
        jsonReader : {
            root: "data"
        },
        prmNames : {
            page: "page",
            rows: "perpage",
            sort: "order_key",
            order: "order_dir"
        },
        search: true,
        colModel:[],
        viewrecords: true,
        rowList : [10,20,50],
        rowNum: 20
    };

    return Backbone.View.extend({

        rowid: null,

        initialize: function(options) {
            $.jgrid.defaults.width = null;
            $.jgrid.defaults.height = null;
            $.jgrid.defaults.autowidth = true;
            $.jgrid.defaults.autoheight = true;
            $.jgrid.defaults.responsive = true;
            $.jgrid.defaults.styleUI = 'Bootstrap';

            options = _.extend(tableConfig, options);
            options.url = this.$el.data('url');
            options.caption = this.$el.data('caption');
            options.colModel = this.model.prototype.gridColumns;
            if (typeof options.colModel === 'function') {
                options.colModel = options.colModel.apply(this.model);
                console.log(options.colModel);
            }


            var $pager, pager = this.$el.data('pager');
            options.pager = pager || '#' + this.$el.attr('id') + 'Pager';
            $pager = $(options.pager);
            if (!$pager.length) {
                $('<div id="'+options.pager.replace('#', '')+'"></div>').insertAfter(this.$el);
            }

            this.model.prototype.urlRoot = options.url;

            options.onSelectRow = $.proxy(this.onSelectRow, this);
            options.gridComplete = $.proxy(this.gridComplete, this);

            this.$el
                .jqGrid(options)
                .jqGrid('filterToolbar', {})
                .navGrid(options.pager, {edit: false, add: false, del: false, search: false});
        },

        reload: function() {
            this.$el.trigger("reloadGrid");
            this.rowid = null;
        },

        onSelectRow: function(rowid) {
            this.rowid = rowid;
        },

        gridComplete: function() {
            this.rowid = null;
        }
    });
});
