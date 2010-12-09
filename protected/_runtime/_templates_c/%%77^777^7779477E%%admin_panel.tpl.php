<?php /* Smarty version 2.6.26, created on 2010-09-24 10:56:24
         compiled from system:catgallery/admin_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'icon', 'system:catgallery/admin_panel.tpl', 2, false),array('function', 'href', 'system:catgallery/admin_panel.tpl', 8, false),)), $this); ?>
<div class="a-gallery">
    <h2><?php echo smarty_function_icon(array('name' => 'images','title' => "Галлерея"), $this);?>
 Галлерея</h2>
    <?php $_from = $this->_tpl_vars['gallery']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <div class="a-gallery-item" <?php if ($this->_tpl_vars['item']['main'] == 1): ?>style="border-color: red;"<?php endif; ?>>
        <div>
            <img width="100" height="100" src="<?php echo $this->_tpl_vars['item']['thumb']; ?>
" alt="<?php echo $this->_tpl_vars['item']['id']; ?>
" title="<?php echo $this->_tpl_vars['item']['image']; ?>
" />
            <div>
                <a <?php echo smarty_function_href(array('url' => "admin/catgallery",'main' => $this->_tpl_vars['item']['id'],'cat' => $this->_tpl_vars['cat']), $this);?>
 class="main_gallery_image"><?php echo smarty_function_icon(array('name' => 'star','title' => "По умолчанию"), $this);?>
</a>
                <a <?php echo smarty_function_href(array('url' => "admin/catgallery",'del' => $this->_tpl_vars['item']['id'],'cat' => $this->_tpl_vars['cat']), $this);?>
 class="del_gallery_image"><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</a>
            </div>
        </div>
    </div>
    <?php endforeach; endif; unset($_from); ?>
    <div class="clear"></div>
    <div class="a-gallery-item-add">
        <?php echo smarty_function_icon(array('name' => 'image_add','title' => "Добавить изображение"), $this);?>

        <a <?php echo smarty_function_href(array('url' => "admin/catgallery",'prod' => $this->_tpl_vars['cat']), $this);?>
 class="gallery-item-add">Добавить изображение</a>
    </div>
    <div class="clear"></div>
</div>