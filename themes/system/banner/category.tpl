
<h3>Список категорий баннеров</h3>

<table class="catalog_data dataset fullWidth">
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th width="120">Править</th>
        <th width="120">Удалить</th>
    </tr>
    {foreach from=$categories item="item"}
        <tr>
            <td width="20">{$item->id}</td>
{*            <td width="20"><p class="page"><a {href catid=$item.id}>{$item->name}</a></p></td>*}
            <td width="20"><p class="page"><a {href controller="banner" action="cat" id=$item->id}>{$item->name}</a></p></td>
            <td>
                <a class="cat_add" {href controller="banner" action="editcat" id=$item->id}>{icon name="pencil" title="Править"}</a>
            </td>
            <td>
                <a {href  controller="banner" action="delcat" id=$item.id} class="do_delete">{icon name="delete" title="Удалить"}</a>
            </td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="6">Пока нет разделов</td>
        </tr>
    {/foreach}
</table>
<p class="page">{$paging.html}</p>
<a class="cat_add" {href controller="banner" action="editcat"}>Добавить категорию</a>

<div id="dlg-form" title="Редактирование баннеров.">
</div>


<script type="text/javascript">
    $(function() {
        $( "#dialog:ui-dialog" ).dialog( "destroy" );
        $( "#dlg-form" ).dialog({
            autoOpen: false,
            width: 735,
            modal: true,
            open: function() {
                wysiwyg.init();
            },
            buttons: {
            "Сохранить": function() {
                var dlg = this;
                $('#form_CategoryBanner').ajaxSubmit({
                    type: 'POST',
                    success: function( response, textStatus, jqXHR ){
                        $( dlg ).dialog( "close" );
                        $.showBlock(response);
                        $.hideBlock(2000);
                        window.location.reload()
                        return true;
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $( dlg ).dialog( "close" );
                        $.showBlock('Данные не сохранены');
                        $.hideBlock(2000);
                        return true;
                    }
                });
            },
            "Отмена": function() {
                $( this ).dialog( "close" );
                $( this ).find('div').remove();
                }
            },

            beforeClose: function(event, ui) {
                $( this ).find('div').remove()
            },
            close: function() {
                $( this ).find('div').remove();
            }
        });

            $('a.cat_add').live('click', function(event) {
                   page_a = this;
                   $( "#dlg-form" ).dialog( "close" );
                   $.post( $(this).attr('href'), function( response, textStatus, jqXHR ) {
                        $( "#dlg-form" ).append('<div>'+response+'</div>').dialog( "open" );
                    });
                return false;
            });
    });
</script>