<form method="post" enctype="multipart/form-data" action="/admin/catgallery/upload={$prod}">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <p>Выбрать файлы: <br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
    </p>
    {*<p><input type="submit" class="submit" value="Отправить" /></p>*}
</form>