/**
 * Модуль для новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("news/admin/news", [
    "jquery",
    "backbone",
    "underscore",
    "system/module/modal",
    "i18n",
    "system/module/alert",
    "news/admin/news_model",
    "system/module/grid_view",
    "jquery-form"
],function($, Backbone, _, Modal, i18n, $alert, NewsModel, GridView) {
    return Backbone.View.extend({

        grid: null,

        "events" : {
            'click a.do_delete' : function( event ){
                var node = this.$(event.target);
                if ( ! confirm(i18n('Want to delete?')) ) {
                    return false;
                }
                try {
                    $.post( $(node).attr('href'), $.proxy(function(response){
                        if (!response.error) {
                            $(node).parents('tr').remove();
                        }
                        $alert(response.msg, 1500);
                    },this), "json");
                } catch (e) {
                    console.error(e.message );
                }
                return false;
            },

            /**
             * Opening edit dialog with loaded content
             * @return {Boolean}
             */
            'click a.catEdit, a.newsEdit' : function( event ) {
                var node = this.$(event.target);
                try {
                    $.get($(node).attr('href')).then($.proxy(function (response) {
                        if ($(node).attr('title')) {
                            this.newsEdit.title($(node).attr('title'));
                        }
                        this.newsEdit.body(response).show();
                    }, this));
                } catch (e) {
                    console.error(e);
                }
                return false;
            },

            'click .btn-edit': function() {
                if (!this.grid.rowid) {
                    $alert('Укажите статью для редактирования', 2000);
                    return false;
                }
                try {
                    $.get("/news/edit?id=" + this.grid.rowid).then($.proxy(function (response) {
                        //if ($(node).attr('title')) {
                        //    this.newsEdit.title($(node).attr('title'));
                        //}
                        this.newsEdit.body(response).show();
                    }, this));
                } catch (e) {
                    console.error(e);
                }
                return false;
            }
        },

        "initialize" : function() {

            this.grid = new GridView({
                el: '#news_grid',
                model: NewsModel
            });

            _.bindAll(this, "onSave", "onSaveSuccess");


            this.newsEdit = new Modal('newsEdit');
            this.newsEdit.onSave(this.onSave);
        },

        onSave: function(){
            $alert("Сохранение", $('.modal-body', this.domnode));
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: this.onSaveSuccess
            });
        },

        onSaveSuccess: function (response) {
            if (!response.error) {
                //$.get(window.location.href, function(response){
                //    var $workspace = $('#workspace');
                //    $workspace.find(':not(h2)').remove();
                //    $workspace.append(response);
                //});
                this.newsEdit.hide();
                this.grid.reload();
                this.msgSuccess(response.msg, 1500);
            } else {
                this.msgError(response.msg);
            }
        }
    });
});
