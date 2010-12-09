<?php /* Smarty version 2.6.26, created on 2010-09-12 03:24:48
         compiled from system:catalog/product.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', 'system:catalog/product.tpl', 4, false),array('function', 'href', 'system:catalog/product.tpl', 76, false),array('modifier', 'strip_tags', 'system:catalog/product.tpl', 45, false),array('modifier', 'string_format', 'system:catalog/product.tpl', 79, false),)), $this); ?>
<?php echo $this->_tpl_vars['breadcrumbs']; ?>


<div class="b-product">
    <form action="<?php echo smarty_function_link(array('url' => 'basket'), $this);?>
" method="post">

        <div class="b-catalog-articul float_right">Артикул: <big><?php echo $this->_tpl_vars['product']['articul']; ?>
</big></div>

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

        <p>&nbsp;</p>

        <table class="b-catalog-product">
          <tr>
            <td colspan="2">
                <input type="hidden" name="basket_prod_id" value="<?php echo $this->_tpl_vars['product']['id']; ?>
" />
                <div class="b-catalog-goback"><a <?php echo smarty_function_href(array('url' => 'catalog','cat' => $this->_tpl_vars['product']['parent']), $this);?>
>Вернуться к списку</a></div>
                <div class="b-catalog-price">
                    Цена <big><?php if ($this->_tpl_vars['user']->getPermission() == @USER_WHOLE && $this->_tpl_vars['product']['price2'] > 0): ?>
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['product']['price2'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>

                    <?php else: ?>
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['product']['price1'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>

                    <?php endif; ?></big> <?php echo $this->_tpl_vars['product']['currency']; ?>

                </div>
                <div class="b-catalog-inbasket">
                    <?php echo $this->_tpl_vars['product']['item']; ?>
 <input type="text" name="basket_prod_count" class="b-catalog-buy-count" value="1" />
                    <input type="submit" class="submit" value="В КОРЗИНУ" />
                </div>
                <div class="clear"></div>
            </td>
          </tr>
        </table>

    </form>
</div>