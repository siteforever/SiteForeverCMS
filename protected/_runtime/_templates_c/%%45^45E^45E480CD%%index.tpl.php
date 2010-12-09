<?php /* Smarty version 2.6.26, created on 2010-09-24 10:39:21
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'head', 'index.tpl', 4, false),array('function', 'menu', 'index.tpl', 18, false),array('function', 'catmenu', 'index.tpl', 24, false),array('function', 'breadcrumbs', 'index.tpl', 31, false),array('modifier', 'default', 'index.tpl', 33, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<?php echo smarty_function_head(array(), $this);?>

</head>
<body class="body">

<div class="b-body">

    <div class="b-body-wrapper">
    
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


        <div class="b-left-panel">
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                <?php echo smarty_function_menu(array('parent' => 0,'level' => 5), $this);?>

            </div>

            <?php if ($this->_tpl_vars['page']['controller'] == 'catalog'): ?>
            <div class="b-left-catmenu">
                <h3>Каталог</h3>
                <?php echo smarty_function_catmenu(array('parent' => 0,'level' => 2,'url' => $this->_tpl_vars['page']['alias']), $this);?>

            </div>
            <?php endif; ?>
        </div>

        <div class="b-content">
        
            <?php echo smarty_function_breadcrumbs(array('path' => $this->_tpl_vars['page']['path']), $this);?>


            <h1><?php echo ((is_array($_tmp=@$this->_tpl_vars['page']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['page']['name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['page']['name'])); ?>
</h1>

            <?php echo $this->_tpl_vars['feedback']; ?>

            
            <?php echo $this->_tpl_vars['page']['content']; ?>

        
        </div>

        <div class="clear"></div>

    </div>
    
    <div class="b-body-footer"></div>

</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</body>
</html>