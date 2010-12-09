<?php /* Smarty version 2.6.26, created on 2010-09-24 17:36:43
         compiled from system:filemanager/table.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:filemanager/table.tpl', 18, false),array('function', 'icon', 'system:filemanager/table.tpl', 24, false),array('function', 'link', 'system:filemanager/table.tpl', 50, false),)), $this); ?>
<p><?php echo $this->_tpl_vars['path_bc']; ?>
</p>


<table class="dataset" width="500">
<tr>
    <th width="400" colspan="3">Наименование</th>
    <th width="100">Размер</th>
</tr>
<?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['file']):
?>
<tr>
    <td width="16">
        <?php if ($this->_tpl_vars['file']['type'] == 'folder'): ?><img src='/images/admin/icons/folder.png' /><?php endif; ?>
        <?php if ($this->_tpl_vars['file']['type'] == 'img'): ?><img src='/images/admin/icons/picture.png' /><?php endif; ?>
        <?php if ($this->_tpl_vars['file']['type'] == 'file'): ?><img src='/images/admin/icons/page.png' /><?php endif; ?>
    </td>
    <td>
        <?php if ($this->_tpl_vars['file']['type'] == 'folder'): ?>
            <a <?php echo smarty_function_href(array('url' => "admin/filemanager",'path' => $this->_tpl_vars['file']['path']), $this);?>
 path="<?php echo $this->_tpl_vars['file']['path']; ?>
"><?php echo $this->_tpl_vars['file']['name']; ?>
</a>
        <?php else: ?>
            <a href="<?php echo $this->_tpl_vars['file']['link']; ?>
" target="_blank" class="gallery"><?php echo $this->_tpl_vars['file']['name']; ?>
</a>
        <?php endif; ?>
    </td>
    <td width="16">
        <a href="#" class="filemanager_delete" delete="<?php echo $this->_tpl_vars['file']['link']; ?>
"><?php echo smarty_function_icon(array('name' => 'delete'), $this);?>
</a>
    </td>
    <td class='right'><?php if ($this->_tpl_vars['file']['type'] == 'folder'): ?>каталог<?php else: ?><?php echo $this->_tpl_vars['file']['size']; ?>
<?php endif; ?></td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="5">файлов не найдено</td>
</tr>
<?php endif; unset($_from); ?>
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

    <form action="<?php echo smarty_function_link(array('url' => "admin/filemanager",'path' => $this->_tpl_vars['path']), $this);?>
" enctype="multipart/form-data" method="post">
        <input type="hidden" name="current_dir" id="current_dir" value="<?php echo $this->_tpl_vars['filedir']; ?>
" />

        <p><b>Загрузить файл</b><br />
            <input type="file" name="upload" /><br />
            <a href="#" class="filemanager_upload">Загрузить файл</a>
        </p>
    </form>
    </td>
</tr>
</table>