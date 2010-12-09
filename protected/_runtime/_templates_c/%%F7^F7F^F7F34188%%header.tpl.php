<?php /* Smarty version 2.6.26, created on 2010-09-24 10:39:18
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'menu', 'header.tpl', 6, false),)), $this); ?>

    <div class="b-header">
        <a class="b-logo" href="/"><img src="<?php echo $this->_tpl_vars['path']['images']; ?>
/siteforever.png" alt="siteforever cms" border="0" /></a>
        
        <div class="b-menu">
            <?php echo smarty_function_menu(array('parent' => 1,'level' => 1), $this);?>

            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>