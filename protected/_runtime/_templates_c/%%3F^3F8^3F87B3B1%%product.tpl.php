<?php /* Smarty version 2.6.26, created on 2010-09-12 03:26:48
         compiled from theme:catalog/product.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', 'theme:catalog/product.tpl', 4, false),array('function', 'href', 'theme:catalog/product.tpl', 60, false),array('modifier', 'strip_tags', 'theme:catalog/product.tpl', 35, false),)), $this); ?>
<?php echo $this->_tpl_vars['breadcrumbs']; ?>


<div class="b-product">
    <form action="<?php echo smarty_function_link(array('url' => 'basket'), $this);?>
" method="post">

        
        <h1><?php echo $this->_tpl_vars['product']['name']; ?>
</h1>

        <div class="b-product-info">
            <div class="b-product-image">
                <?php if ($this->_tpl_vars['product']['image'] != ''): ?>
                <a href="<?php echo $this->_tpl_vars['product']['image']; ?>
" target="_blank">
                    <img src="<?php echo $this->_tpl_vars['product']['image']; ?>
" width="200" class="float_left" alt="<?php echo $this->_tpl_vars['product']['name']; ?>
" />
                </a>
                <?php else: ?>
                <p>Нет изображения</p>
                <?php endif; ?>
            </div>

            <div class="b-product-details">

                                <?php if (count ( $this->_tpl_vars['properties'] ) > 0): ?>
                    <table class="b-catalog-product-properties">
                    <?php $_from = $this->_tpl_vars['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pkey'] => $this->_tpl_vars['pitem']):
?>
                    <tr>
                        <td width="100"><?php echo $this->_tpl_vars['pkey']; ?>
</td>
                        <td width="10"></td>
                        <td width="200"><?php echo $this->_tpl_vars['pitem']; ?>
</td>
                    </tr>
                    <?php endforeach; endif; unset($_from); ?>
                    </table>
                <?php endif; ?>
                <?php if (trim ( ((is_array($_tmp=$this->_tpl_vars['product']['text'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)) ) != ''): ?>
                <div class="b-description">
                    <p><strong>Описание</strong></p>

                    <div>
                    <?php echo $this->_tpl_vars['product']['text']; ?>

                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="clear"></div>
        </div>

        <?php if (count ( $this->_tpl_vars['gallery'] ) > 1): ?>
            <ul class="b-product-gallery">
                <?php $_from = $this->_tpl_vars['gallery']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                <li><a href="<?php echo $this->_tpl_vars['item']['image']; ?>
" class="gallery" rel="image_group" target="_blank" ">
                    <img src="<?php echo $this->_tpl_vars['item']['thumb']; ?>
" alt="" />
                </a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
        <?php endif; ?>

<p><a <?php echo smarty_function_href(array('cat' => $this->_tpl_vars['product']['parent']), $this);?>
>Вернуться к списку</a></p>
    </form>
</div>