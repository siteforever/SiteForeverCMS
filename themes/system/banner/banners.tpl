
<h3> <p class="page"><a {href controller="banner" action="admin"}>Список категорий баннеров </a>
&rarr; {$cat.name}</h3><br />

<table class="catalog_data dataset fullWidth">
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th width="120">Количество показов</th>
        <th width="120">Количество переходов</th>
        <th width="120">Править</th>
        <th width="120">Удалить</th>
    </tr>
    {foreach from=$banners item="item"}
        <tr>
            <td width="20">{$item->id}</td>
            <td width="20"><p class="page">{$item->name}</p></td>
            <td width="20">{if $item->count_show}{$item->count_show}{else}0{/if}</td>
            <td width="20">{if $item->count_click}{$item->count_click}{else}0{/if}</td>
            <td>
{*                <a class="add_meta" {href id=$item.id}>{icon name="pencil" title="Править"}</a>*}
                <a class="ban_add" {href controller="banner" action="edit" id=$item.id}>{icon name="pencil" title="Править"}</a>
{*                <a {href  controller="banner" action="edit" id=$item.id}>{icon name="pencil" title="Править"}</a>*}
            </td>
            <td>
                <a {href  controller="banner" action="del" id=$item.id} >{icon name="delete" title="Удалить"}</a>
            </td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="6">Пока нет разделов</td>
        </tr>
    {/foreach}
</table>
<p class="page">{$paging.html}</p>
{*<a class="add_meta" {href controller="banner" action="edit"}>Добавить баннер</a>*}
<br />
<form id="load_images" action="{link}" method="post" enctype="multipart/form-data">
{*<form id="load_images" action={href controller="banner" action="edit" id=$cat.id} method="post" enctype="multipart/form-data">*}
    <div class="newimage">
        Наименование: <input type="text" name="name[]" />
        Файл: <input type="file" name="image[]" />
    </div>
    </form>

    <br />
    <p>
        <button id="add_image">{icon name="picture_add"} Добавить</button> |
        <button id="send_images">{icon name="picture_save"} Отправить</button>
    </p>
<div id="dialog-form" title="Редактирование баннеров.">
</div>


<script type="text/javascript">
    $(function() {
        var reserv_img = $("div.newimage:last").clone();
        $("#add_image").click(function(){
            $(reserv_img).clone().appendTo("#load_images");
            return false;
        });
        $("#send_images").click(function(){
            $("#load_images").submit();
            return false;
        });

        $( "#dialog:ui-dialog" ).dialog( "destroy" );
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            width: 735,
            modal: true,
            open: function() {
                wysiwyg.init();
            },
            buttons: {
            "Сохранить": function() {
                var dlg = this;
                $('#form_Banner').ajaxSubmit({
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

            $('a.ban_add').live('click', function(event) {
                   page_a = this;
                   $( "#dialog-form" ).dialog( "close" );
                   $.post( $(this).attr('href'), function( response, textStatus, jqXHR ) {
                        $( "#dialog-form" ).append('<div>'+response+'</div>').dialog( "open" );
                    });
                return false;
            });
    });
</script>