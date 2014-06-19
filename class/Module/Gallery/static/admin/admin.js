/**
 * Управление админкой галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
define("gallery/admin/admin", [
    "jquery",
    "backbone",
    "wysiwyg",
    "system/module/modal",
    "i18n",
    "system/module/alert",
    "system/admin/confirm/delete",
    "jquery-ui"
], function ($, Backbone, wysiwyg, Modal, i18n, $alert, deleteConfirm) {
    return Backbone.View.extend({
        "progressTpl": '<div class="progress progress-striped active"><div class="progress-bar" style="width: 100%"></div></div>',

        "events": {
            "click div.gallery_name": function (event) {
                event.stopPropagation();
                var node = event.currentTarget,
                    val = $(node).find('input').val(),
                    name = $(node).find('input').attr('name');

                $(node).find('span').hide().next().hide();

                $("<input type='text' name='" + name + "' value='" + val + "' data-old='" + val + "'>").prependTo(node).focus();

                $('input:text', node)
                    .blur(this.nameApply)
                    .click(function (event) {
                        return false;
                    })
                    .keypress($.proxy(function (event) {
                        if (event.keyCode == '13') {
                            this.nameApply.call($('input:text', node)[0]);
                        }
                        if (event.keyCode == '27') {
                            this.nameCancel.call($('input:text', node)[0]);
                        }
                    }, this));
            },

            "click a.do_delete": function () {
                return confirm(i18n('Want to delete?'));
            },

            "click a.gallery_picture_edit": function (event) {
                var node = event.currentTarget;
                this.editImage.title('Правка информации').body(this.progressTpl).show();
                $.post($(node).attr('href'), $.proxy(function (response) {
                    this.editImage.deInit().body(response).init();
                }, this));
                return false;
            },

            "click a.gallery_picture_delete": deleteConfirm,

            // Переключение активности изображения
            "click a.gallery_picture_switch": function (event) {
                var node = event.currentTarget;
                $.post($(node).attr('href'), function (response) {
                    try {
                        if (response.error == '0' && response.id) {
                            $('#gallery li[rel=' + response.id + '] a.gallery_picture_switch').html(response.img);
                        } else {
                            $alert(response.msg);
                        }
                    } catch (e) {
                        $alert(e.message)
                    }
                }, 'json');
                return false;
            },

            "click #add_image": function (event) {
                event.preventDefault();
                $(this.reservImg).clone().appendTo("#load_images");
                return false;
            },

            "click #send_images": function (event) {
                event.preventDefault();
                $("#load_images").submit();
            },

            "click a.editCat": function (event) {
                event.preventDefault();
                var node = event.currentTarget;
                this.editCat.title('Правка информации').body(this.progressTpl).show();
                $.get($(node).attr('href'), $.proxy(function (response) {
                    this.editCat.title($(node).attr('title')).deInit().body(response).init();
                }, this));
            }
        },

        "initialize": function() {
            // Сортировка
            $("#gallery").sortable({
                update: function (event, ui) {
                    var positions = [];
                    $(this).find('li').each(function () {
                        positions.push($(this).attr('rel'));
                    });
                    $.post('/gallery/admin', { positions: positions });
                }
            }).disableSelection();

            this.editImage = new Modal('editImage');
            this.editImage.onSave($.proxy(this.onSave, this.editImage));

            // Создание мультизагрузки
            this.reservImg = $("div.newimage:last").clone();

            // Управление списком галерей
            this.editCat = new Modal('editCat');
            this.editCat.onSave($.proxy(this.onSaveCat, this.editCat));
        },

        /**
         * Редактировать название и применить
         */
        nameApply: function () {
            var val = $(this).val(),
                old = $(this).attr('data-old'),
                id = $(this).parent().attr('rel');
            if (id && val != old) {
                $.post('/gallery/admin', { editimage: id, name: val }, function(){
                    $.growlUI(i18n('Save successfully'));
                });
            }
            $(this).parent().find('span').text(val).show().next().show().next().val(val);
            $(this).remove();
        },

        /**
         * Редактировать название и отменить
         */
        nameCancel: function () {
            var val = $(this).attr('data-old');
            $(this).parent().find('span').text(val).show().next().show();
            $(this).remove();
        },

        /**
         * Созранить данные об изображении
         */
        onSave: function () {
            $('form', this.domnode).ajaxSubmit({
                dataType: "json",
                success: $.proxy(function (response) {
                    if (!response.error) {
                        this.msgSuccess(response.msg, 1500);
                        var domName = $('#gallery').find('li[rel=' + response.id + ']').find('div.gallery_name');
                        $('span', domName).text(response.name);
                        $('input.gallery_name_field', domName).val(response.name);
                        $.growlUI(i18n('Save successfully'));
                    } else {
                        this.msgError(response.msg);
                    }
                }, this)
            });
        },

        /**
         * Сохранить данные об категории
         */
        onSaveCat: function () {
            $('form', this.domnode).ajaxSubmit({
                dataType: "json",
                success: $.proxy(function (response) {
                    if (!response.error) {
                        this.msgSuccess(response.msg, 1500);
                        $('a[rel=' + response.id + ']').text(response.name);
                        $.growlUI(i18n('Save successfully'));
                    } else {
                        this.msgError(response.msg);
                    }
                }, this)
            });
        }
    });
});
