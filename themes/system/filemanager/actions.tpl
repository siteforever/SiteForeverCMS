
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