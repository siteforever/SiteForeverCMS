<p>{$path_bc}</p>


<ul class="filemanager tile">

{foreach from=$files item="file"}

<li>
    {if $file.type=='folder'}
        <a {href url="admin/filemanager" path=$file.path} path="{$file.path}"><img src='/images/folder100.png' alt='Каталог' /> {$file.name}</a>
    {/if}
    {if $file.type=='img'}
        <a href="{$file.link}" target="_blank" class="gallery">
            <img src="{$file.link}" width="100" height="100" border="0" />
        </a>
        <br />
        {$file.imagesize.0} X {$file.imagesize.1}<br />
        <a href="{$file.link}" target="_blank" class="gallery">{$file.name}</a>
    {/if}
    {if $file.type=='file'}
        <a href="{$file.link}" target="_blank" class="gallery">{$file.name}</a>
    {/if}
</li>

{/foreach}

</ul>


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