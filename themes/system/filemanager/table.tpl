<p>{$path_bc}</p>


<table class="dataset" width="500">
<tr>
    <th width="400" colspan="3">Наименование</th>
    <th width="100">Размер</th>
</tr>
{foreach from=$files item="file"}
<tr>
    <td width="16">
        {if $file.type=='folder'}<img src='/images/admin/icons/folder.png' />{/if}
        {if $file.type=='img'}<img src='/images/admin/icons/picture.png' />{/if}
        {if $file.type=='file'}<img src='/images/admin/icons/page.png' />{/if}
    </td>
    <td>
        {if $file.type == 'folder'}
            <a {href url="admin/filemanager" path=$file.path} path="{$file.path}">{$file.name}</a>
        {else}
            <a href="{$file.link}" target="_blank" class="gallery">{$file.name}</a>
        {/if}
    </td>
    <td width="16">
        <a href="#" class="filemanager_delete" delete="{$file.link}">{icon name="delete"}</a>
    </td>
    <td class='right'>{if $file.type=='folder'}каталог{else}{$file.size}{/if}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="5">файлов не найдено</td>
</tr>
{/foreach}
</table>


<p></p>

<table width="500">
<tr>
    <td>
        <p>
            <b>Создать каталог</b><br />
            <input name="new_dir" id="new_dir" value="new_folder" /><br />
            <small>Только символы: a-z, 0-9, &laquo;.&raquo;, &laquo;_&raquo;, &laquo;-&raquo;</small><br />
            <a href="#" class="filemanager_new_catalog">Создать</a>
        </p>
    </td>
    <td>

    <form action="{link url="admin/filemanager" path=$path}" enctype="multipart/form-data" method="post">
        <input type="hidden" name="current_dir" id="current_dir" value="{$filedir}" />

        <p><b>Загрузить файл</b><br />
            <input type="file" name="upload" /><br />
            <a href="#" class="filemanager_upload">Загрузить файл</a>
        </p>
    </form>
    </td>
</tr>
</table>