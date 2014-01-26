/**
 * Collection for admin item model
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("Model/AdminItemCollection", [
    "backbone",
    "Model/AdminItem"
], function(Backbone, AdminItemModel){
    return Backbone.Collection.extend({
        model: AdminItemModel,

        baseUrl: '',
        pages: 1,
        page: 1,
        orderDirs: {},
        order: false,
        dir: 'asc',

        filter: {},

        url: function() {
            var urlComponents = [];

            if (this.page > 2) {
                urlComponents.push('page=' + this.page);
            }

            if (this.order) {
                urlComponents.push('o=' + this.order);
                urlComponents.push('dir=' + this.dir);
            }
            if (this.filter) {
                for (var key in this.filter) {
                    if (this.filter.hasOwnProperty(key) && this.filter[key].length >= 2) {
                        urlComponents.push('filter[' + key + ']=' + this.filter[key]);
                    }
                }
            }
            return this.baseUrl + (urlComponents.length ? '?' + urlComponents.join('&') : '');
        },

        // for delegation from DataGridView
        onSort: function(event) {
            var $node = $(event.target),
                col   = $node.data('ord'),
                dir   = this.orderDirs[col] ? this.orderDirs[col] : 'desc';

            if (this.order == col) {
                dir = ('desc' == dir) ? 'asc' : 'desc';
            } else {
                this.order = col;
            }
            this.orderDirs[col] = dir;
            this.dir = dir;
            this.fetch();
            return false;
        },

        // for delegation from DataGridView
        onFilter: function(event) {
            var filter = this.filter;

            if (event.keyCode < 32 && event.keyCode != 8) {
                return false;
            }

            var $target = $(event.target),
                col = $target.data('col'),
                val = $.trim($target.val());

            if (val == filter[col] || !(val || filter[col])) {
                return false;
            }

            if (val) {
                filter[col] = val;
            } else {
                delete filter[col];
            }

            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            if (val.length > 0 && val.length < 2) {
                return false;
            }
            this.filter = filter;
            this.timeout = setTimeout($.proxy(this.fetch, this), 500);
            return true;
        }
    });
});
