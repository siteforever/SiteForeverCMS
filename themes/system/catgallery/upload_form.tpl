<form method="post" enctype="multipart/form-data" action="{link url="catgallery/upload" prod_id=$prod_id}">
    <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}" />
    <input type="hidden" name="sent" value="1"/>
    <p>Выбрать файлы: <br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
    </p>
</form>