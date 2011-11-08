<p><a {href url="catalog/admin"}><span>Каталог</span></a> &gt; <span>Загрузить прайслист</span></p>

<br />

<form action="{link price="load"}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />

    <p>Выберите XML файл:</p>
    <p><input type="file" name="xml_file" /></p>
    <p><input type="submit" value="Загрузить" /></p>
</form>

{if $mark_del}
<br />
<p><strong>Не содержатся в прайсе:</strong></p>

<form action="{link delete="group"}" method="post" id="trade_delete_form">
    <table width="500" class="dataset">
        <tr>
            <th width="150">Артикул</th>
            <th>Наименование</th>
        </tr>
        {foreach from=$mark_del item="t"}
        <tr rel="trade_delete_{$t.id}">
            <td>
                <input type=checkbox class="checkbox trade_delete" id="trade_delete_{$t.id}" name="trade_delete[]" value="{$t.id}" />
                <label for="trade_delete_{$t.id}">{$t.articul}</label>
            </td>
            <td>{$t.name}</td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="3">Ничего удалять не надо</td>
        </tr>
        {/foreach}
    </table>
    <br />
    <p><input type="submit" value="Удалить выбранные" /></p>
</form>
{literal}
<script type="text/javascript">
$(function(){
    $("#trade_delete_form").ajaxForm({
        success: function ( data, p1, p2 ) {
            var arr_data    = data.split(',');
            if ( data.match(',') ) {
                for( i in arr_data ) {
                    $('tr[rel=trade_delete_'+arr_data[i]+']').remove();
                }
            } else {
                alert(data);
            }
        }
    });
})
</script>
{/literal}
{/if}