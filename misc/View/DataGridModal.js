/**
 * Modal window for admin interface
 * @base View/Modal
 */
define('View/DataGridModal', [
    'View/Modal',
    'i18n'
], function(ModalView, i18n){
    return ModalView.extend({
        events: {
            'click .btn-close': 'onClickClose',
            'click .btn-save': 'onClickSave',
            'keyup :input': 'onType',
            'click :checkbox': 'onCheck',
            'mousedown div.modal-header': 'onMoveStart'
        },

        buttons: [
            {
                text: i18n('Save'),
                classes: 'btn btn-primary btn-save'
            },
            {
                text: i18n('Close'),
                classes: 'btn btn-close'
            }
        ],

        content: function() {
            return '<div class="progress progress-striped active">'
                    + '<div class="bar" style="width: 100%;"></div>'
                 + '</div>';
        },

        onClickSave: function() {
            this.$el.find('.btn-save').addClass('disabled')/*.prepend('<i class="icon icon-time"></i>')*/;
            this.$el.find('form').ajaxSubmit({
                dataType: 'json',
                success: $.proxy(this.onSaveSuccess, this)
            });
        },

        onSaveSuccess: function(response) {
            if ('ok' == response.status) {
                this.hide();
            }
            var i,
                err = response.errors,
                $elem;

            // Mark error field
            this.$el.find('.control-group').removeClass('error').find('.help-inline').remove();
            for (i in err) {
                if (err.hasOwnProperty(i)) {
                    $elem = this.$el.find('div[data-field-name="'+i+'"]');
                    $elem.addClass('error').find('.controls').append(
//                    this.make('span', {class: 'help-inline'}, err[i])
                        '<span class="help-inline">' + err[i] + '</span>'
                    );
                }
            }

            this.$el.find('.btn-save').removeClass('disabled');

            if (this.options.dispatcher && !err) {
                this.options.dispatcher.trigger('admin.model.save', this.model);
            }
        },

        onClickClose: function(){
            this.model.set(this.codyData);
            this.hide();
        }

    });
});
