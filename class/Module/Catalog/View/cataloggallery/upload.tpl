<form method="post" enctype="multipart/form-data" action="{link url="cataloggallery/upload" prod_id=$prod_id}">
    <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}" />
    <input type="hidden" name="sent" value="1"/>
    <p>Выбрать файлы: <br />
        <input type="file" name="image[]" multiple="multiple" /><br />
        <input type="file" name="image[]" multiple="multiple" /><br />
        <input type="file" name="image[]" multiple="multiple" />
    </p>
</form>