<form method="post" enctype="multipart/form-data" action="{link url="admin/catgallery" upload=$prod}">
    <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}" />
    <p>Выбрать файлы: <br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
    </p>
</form>