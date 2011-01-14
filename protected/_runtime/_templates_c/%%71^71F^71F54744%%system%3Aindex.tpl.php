<?php /* Smarty version 2.6.26, created on 2011-01-14 11:23:29
         compiled from system:index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 't', 'system:index.tpl', 4, false),array('function', 'icon', 'system:index.tpl', 55, false),array('function', 'href', 'system:index.tpl', 56, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo smarty_function_t(array('text' => 'Control panel'), $this);?>
 :: <?php echo $this->_tpl_vars['page']['title']; ?>
</title>

<meta content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ru" />

<link rel="icon" type="image/png" href="http://<?php echo $this->_tpl_vars['host']; ?>
/favicon.png" />
<link rel="apple-touch-icon-precomposed" href="http://<?php echo $this->_tpl_vars['host']; ?>
/apple-touch-favicon.png" />

<style type="text/css">@import url("<?php echo $this->_tpl_vars['path']['misc']; ?>
/reset.css");</style>
<style type="text/css">@import url("<?php echo $this->_tpl_vars['path']['misc']; ?>
/siteforever.css");</style>
<style type="text/css">@import url("<?php echo $this->_tpl_vars['path']['misc']; ?>
/smoothness/jquery-ui.css");</style>
<style type="text/css">@import url("<?php echo $this->_tpl_vars['path']['misc']; ?>
/admin.css");</style>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/jquery.blockUI.js"></script>



<style type="text/css">@import url("<?php echo $this->_tpl_vars['path']['misc']; ?>
/fancybox/jquery.fancybox-1.3.1.css");</style>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/fancybox/jquery.fancybox-1.3.1.pack.js"></script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/ckeditor/adapters/jquery.js"></script>


<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/forms.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/admin.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/catalog.js"></script>
</head>

<body class="body">
<div class="l-wrapper">

    <h1><?php echo smarty_function_t(array('text' => 'Control panel'), $this);?>
 :: <?php echo $this->_tpl_vars['page']['title']; ?>
</h1>
    
    <div class="l-main-panel">
        <div class="l-panel">
	        <ul class="b-admin-menu">
	            <?php $_from = $this->_tpl_vars['request']->get('modules'); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                <li>
                    <?php if ($this->_tpl_vars['item']['icon']): ?><?php echo smarty_function_icon(array('name' => $this->_tpl_vars['item']['icon']), $this);?>
<?php endif; ?>
                    <a  <?php if ($this->_tpl_vars['item']['norefact']): ?>href="<?php echo $this->_tpl_vars['item']['url']; ?>
"<?php else: ?><?php echo smarty_function_href(array('url' => $this->_tpl_vars['item']['url']), $this);?>
<?php endif; ?>
                        <?php if ($this->_tpl_vars['item']['class'] != ''): ?>class="<?php echo $this->_tpl_vars['item']['class']; ?>
"<?php endif; ?>
                        <?php if ($this->_tpl_vars['item']['target']): ?>target="<?php echo $this->_tpl_vars['item']['target']; ?>
"<?php endif; ?> >
                        <?php echo $this->_tpl_vars['item']['name']; ?>

                    </a>
                    <?php if (isset ( $this->_tpl_vars['item']['sub'] )): ?>
                        <ul>
                        <?php $_from = $this->_tpl_vars['item']['sub']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['subitem']):
?>
                        <li>
                            <?php if ($this->_tpl_vars['subitem']['icon']): ?><?php echo smarty_function_icon(array('name' => $this->_tpl_vars['subitem']['icon']), $this);?>
<?php endif; ?>
                            <a  <?php if ($this->_tpl_vars['subitem']['norefact']): ?>href="<?php echo $this->_tpl_vars['subitem']['url']; ?>
"<?php else: ?><?php echo smarty_function_href(array('url' => $this->_tpl_vars['subitem']['url']), $this);?>
<?php endif; ?>
                                <?php if ($this->_tpl_vars['subitem']['class'] != ''): ?>class="<?php echo $this->_tpl_vars['subitem']['class']; ?>
"<?php endif; ?>
                                <?php if ($this->_tpl_vars['subitem']['target']): ?>target="<?php echo $this->_tpl_vars['subitem']['target']; ?>
"<?php endif; ?> >
                                <?php echo $this->_tpl_vars['subitem']['name']; ?>

                            </a>
                        </li>
                        <?php endforeach; endif; unset($_from); ?>
                        </ul>
                    <?php endif; ?>
                </li>
	            <?php endforeach; endif; unset($_from); ?>

	        </ul>
        </div>

        <div class="l-content">
            <?php if ($this->_tpl_vars['feedback']): ?><p class="red"><?php echo $this->_tpl_vars['feedback']; ?>
</p><?php endif; ?>

            <?php echo $this->_tpl_vars['page']['content']; ?>


            <div class="l-content-wrapper"></div>
        </div>

        <div class="clear"></div>
    </div>

    <div class="l-footer-wrapper"></div>

</div>
<div class="l-footer">
    <a href="http://siteforever.ru" target="_blank"><?php echo smarty_function_t(array('text' => 'Working on'), $this);?>
 &copy; SiteForeverCMS</a> <small><?php echo smarty_function_t(array('text' => 'Memory'), $this);?>
:<?php echo $this->_tpl_vars['memory']; ?>
, <?php echo smarty_function_t(array('text' => 'Generation'), $this);?>
:<?php echo $this->_tpl_vars['exec']; ?>
</small>
</div>

</body>
</html>