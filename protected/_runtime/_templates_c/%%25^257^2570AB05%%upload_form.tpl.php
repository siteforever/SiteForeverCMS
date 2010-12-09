<?php /* Smarty version 2.6.26, created on 2010-09-24 10:47:31
         compiled from system:catgallery/upload_form.tpl */ ?>
<form method="post" enctype="multipart/form-data" action="/admin/catgallery/upload=<?php echo $this->_tpl_vars['prod']; ?>
">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <p>Выбрать файлы: <br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
        <input type="file" name="image[]" /><br />
    </p>
    </form>