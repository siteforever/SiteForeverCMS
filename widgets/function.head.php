<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.head.php
 * Type:     function
 * Name:     head
 * Purpose:  Печатает заголов head на сайте
 * -------------------------------------------------------------
 * @example {head}
 */
function smarty_function_head( $params )
{
    $app        = App::getInstance();
    $request    = $app->getRequest();
    $config     = $app->getConfig();
    $settings   = $app->getSettings();

    $head = array();
    $head[] = "<title>".strip_tags( $request->getTitle() ).' / '.$config->get('sitename')."</title>";

    if ( $request->get('keywords') ) {
        $head[] = "<meta name=\"keywords\" content=\"".$request->get('keywords')."\" />";
    }
    if ( $request->get('description') ) {
        $head[] = "<meta name=\"description\" content=\"".$request->get('description')."\" />";
    }
    
    $head[] = "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />";
    $head[] = "<link title=\"\" type=\"application/rss+xml\" rel=\"alternate\" href=\"http://{$_SERVER['HTTP_HOST']}/rss\" />";

    if ( file_exists( ROOT.DS.'favicon.png' ) ) {
        $head[] = "<link rel=\"icon\" type=\"image/png\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.png\" />";
    } elseif ( file_exists( ROOT.DS.'favicon.ico' ) ) {
        $head[] = "<link rel=\"icon\" type=\"image/ico\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.ico\" />";
    }

    $useLess = false;
    if ( $request->get('admin') ) {
        $app->addStyle('/misc/jqGrid/css/ui.jqgrid.css');
    }

    foreach( $app->getStyle() as $style ) {
        if ( preg_match('/.*\.css$/', $style) ) {
            $head[ ] = "<link type=\"text/css\" rel=\"stylesheet\" href=\"$style\">";
        } elseif ( preg_match('/.*\.less$/', $style) ) {
            $head[ ] = "<link type=\"text/css\" rel=\"stylesheet/less\" href=\"$style\">";
            $useLess = true;
        }
    }

    $rjsConfig = array(
        'baseUrl'=> '/misc',
        'shim' => array(
            'jui'   => array('jquery'),
            'etc/catalog' => array('jquery','jquery/jquery.gallery'),
            'jquery/jquery.gallery' => array('jquery','fancybox'),
        ),
        'paths'=> array(
            'fancybox' => 'jquery/fancybox/jquery.fancybox-1.3.1' . (App::isDebug() ? '' : '.pack'),
            'jui' => 'jquery/jquery-ui-'.Sfcms_View_Layout::JQ_UI_VERSION.'.custom.min',
            'twitter' => 'bootstrap/js/bootstrap' . (App::isDebug() ? '' : '.min'),
            'siteforever' => 'module/siteforever',
            'runtime' => '../_runtime',
            'i18n'  => '../_runtime/i18n.ru',
            'theme' => '/themes/'.App::getInstance()->getConfig('template.theme'),
        ),
    );


    if ( $request->get('admin') ) {
        $rjsConfig['paths']['app'] = 'admin';
        $rjsConfig['paths']['controller'] = 'admin/'.$request->getController();
        $rjsConfig['paths']['wysiwyg'] = 'admin/editor/'.$settings->get('editor', 'type'); // tinymce, ckeditor, elrte
        $rjsConfig['shim']['elfinder/js/i18n/elfinder.ru'] = array('elfinder/js/elfinder' . (App::isDebug() ? '.full' : '.min'));
        $rjsConfig['shim']['ckeditor/adapters/jquery'] = array('ckeditor/ckeditor');
        $rjsConfig['shim']['jquery/jquery.jqGrid'] = array(
            'jqGrid/js/i18n/grid.locale-ru',
            'jqGrid/js/grid.base',
            'jqGrid/js/grid.common',
//            'jqGrid/js/grid.formedit',
//            'jqGrid/js/grid.inlinedit',// => array('deps'=> array('jqGrid/js/grid.base')),
//            'jqGrid/js/grid.celledit',
//            'jqGrid/js/grid.subgrid',
            'jqGrid/js/grid.treegrid',
            'jqGrid/js/grid.grouping',
            'jqGrid/js/grid.custom',
            'jqGrid/js/grid.tbltogrid',
            'jqGrid/js/grid.import',
            'jqGrid/js/jquery.fmatter',
            'jqGrid/js/JsonXml',
            'jqGrid/js/grid.jqueryui',
            'jqGrid/js/grid.filter',
        );

        $head[] = '<script type="text/javascript">var require = '.json_encode($rjsConfig).';</script>';


        if ( file_exists(SF_PATH.'/_runtime/asset/require-jquery-min.js') ) {
            $head[] = "<script type='text/javascript' "
                    . "src='/_runtime/asset/require-jquery-min.js' data-main='../_runtime/asset/admin-min'>"
                    . "</script>";
        } else {
            $head[] = "<script type='text/javascript' "
                    . "src='/misc/require-jquery.js' data-main='admin/app'>"
                    . "</script>";
        }


    } else {
        $head[] = '<script type="text/javascript">var require = '.json_encode($rjsConfig).';</script>';
        $head[] = "<script type='text/javascript' src='/misc/require-jquery.js' data-main='site'></script>";
    }



    if ( $useLess ) {
        $head[] = '<script type="text/javascript" src="/misc/less-1.3.0.min.js"></script>';
    }

    $head[] = "<meta name=\"generator\" content=\"SiteForever CMS\" />";

    return join("\n", $head);
}