<?php /* Smarty version 2.6.26, created on 2010-09-12 03:18:52
         compiled from system:catalog/goods.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:catalog/goods.tpl', 17, false),array('modifier', 'string_format', 'system:catalog/goods.tpl', 59, false),)), $this); ?>
<?php echo $this->_tpl_vars['breadcrumbs']; ?>


<div class="catalog_order">
    Сортировать
    <select class="catalog_select_order">
        <?php $_from = $this->_tpl_vars['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ord_key'] => $this->_tpl_vars['ord']):
?>
        <option value="<?php echo $this->_tpl_vars['ord_key']; ?>
" <?php if ($this->_tpl_vars['order_val'] == $this->_tpl_vars['ord_key']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['ord']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
    </select>
</div>

<div class="clear"></div>

<?php if ($this->_tpl_vars['cats']): ?>
<ul class="b-cat-list">
    <?php $_from = $this->_tpl_vars['cats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
    <li><a <?php echo smarty_function_href(array('url' => 'catalog','cat' => $this->_tpl_vars['cat']['id']), $this);?>
><?php echo $this->_tpl_vars['cat']['name']; ?>
 </a></li>
    <?php endforeach; endif; unset($_from); ?>
</ul>
<?php endif; ?>


<?php $_from = $this->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<form action="/basket/" method="post">
    <table class="b-catalog-product fullWidth">
      <tr>
        <td width="110">
            <?php if ($this->_tpl_vars['item']['image']): ?>
                <a <?php echo smarty_function_href(array('cat' => $this->_tpl_vars['item']['id']), $this);?>
>
                    <img class="left" src="<?php echo $this->_tpl_vars['item']['thumb']; ?>
" alt="<?php echo $this->_tpl_vars['item']['name']; ?>
" bprder="0" width="100" height="100" />
                </a>
            <?php else: ?>
                <div class="b-catalog-product-noimage"><br /><br />Нет изображения</div>
            <?php endif; ?>
        </td>
        <td>
            <p class="b-catalog-product-title"><a <?php echo smarty_function_href(array('cat' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo $this->_tpl_vars['item']['name']; ?>
</a></p>
            <div class="b-catalog-articul">Артикул <big><?php echo $this->_tpl_vars['item']['articul']; ?>
</big></div>
                        <?php if (count ( $this->_tpl_vars['item']['properties'] ) > 0): ?>
                <table class="b-catalog-product-properties">
                <?php $_from = $this->_tpl_vars['item']['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
            <div class="b-catalog-product-desc"><?php echo $this->_tpl_vars['item']['text']; ?>
</div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
            <input type="hidden" name="basket_prod_id" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />
            <div class="b-catalog-price">
                Цена <big><?php if ($this->_tpl_vars['user']->getPermission() == @USER_WHOLE && $this->_tpl_vars['item']['price2'] > 0): ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['price2'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>

                <?php else: ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['price1'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>

                <?php endif; ?></big> <?php echo $this->_tpl_vars['item']['currency']; ?>

            </div>
            <div class="b-catalog-inbasket">
                <?php echo $this->_tpl_vars['item']['item']; ?>
 <input type="text" name="basket_prod_count" class="b-catalog-buy-count" value="1" />
                <input type="submit" class="submit" value="В КОРЗИНУ" />
            </div>
            <div class="clear"></div>
        </td>
      </tr>
    </table>
</form>

<hr class="b-catalog-separator" />

<?php endforeach; else: ?>
    <?php if (count ( $this->_tpl_vars['cats'] ) == 0): ?>
    <p>Товаров не найдено</p>
    <?php endif; ?>
<?php endif; unset($_from); ?>

<p><?php echo $this->_tpl_vars['paging']['html']; ?>
</p>