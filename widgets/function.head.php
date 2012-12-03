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

    $head = array();
    $head[] = "<title>".strip_tags( $request->getTitle() ).' / '.$config->get('sitename')."</title>";

    if ( $request->getKeywords() ) {
        $head[] = "<meta name=\"keywords\" content=\"".$request->getKeywords()."\" />";
    }
    if ( $request->getDescription() ) {
        $head[] = "<meta name=\"description\" content=\"".$request->getDescription()."\" />";
    }
    
    $head[] = "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />";
    $head[] = "<link title=\"\" type=\"application/rss+xml\" rel=\"alternate\" href=\"http://{$_SERVER['HTTP_HOST']}/rss\" />";

    if ( file_exists( ROOT.DS.'favicon.png' ) ) {
        $head[] = "<link rel=\"icon\" type=\"image/png\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.png\" />";
    }
    if ( file_exists( ROOT.DS.'favicon.ico' ) ) {
        $head[] = "<link rel=\"icon\" type=\"image/ico\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.ico\" />";
    }

    if ( $request->get('admin') ) {
        $app->addStyle('/misc/jqGrid/css/ui.jqgrid.css');
    }

    $useLess = false;
    $head = array_merge( $head, array_map(function($style) use ( &$useLess ) {
        if ( preg_match('/.*\.css$/', $style) ) {
            return "<link type=\"text/css\" rel=\"stylesheet\" href=\"$style\">";
        }
        if ( preg_match('/.*\.less$/', $style) ) {
            $useLess = true;
            return "<link type=\"text/css\" rel=\"stylesheet/less\" href=\"$style\">";
        }
        return '';
    }, $app->getStyle()) );

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
            'theme' => '/themes/'.App::getInstance()->getConfig('template.theme'),
            'i18n'  => '../_runtime/i18n.'.$app->getConfig('language'),
        ),
        'map' => array(
            '*' => array(
            ),
        ),
    );


    if ( $request->get('admin') ) {
        $rjsConfig['paths']['app'] = 'admin';
//        $rjsConfig['paths']['controller'] = ;
        $rjsConfig['shim']['elfinder/js/i18n/elfinder.ru'] = array('elfinder/js/elfinder');
        $rjsConfig['shim']['ckeditor/adapters/jquery'] = array('ckeditor/ckeditor');

        $rjsConfig['map']['*'] += array(
            'wysiwyg' => 'admin/editor/'.($config->get('editor')?:'ckeditor'), // tinymce, ckeditor, elrte
            'elfinder/js/elfinder' => 'elfinder/js/elfinder' . (App::isDebug() ? '.full' : '.min'),
//            'jqgrid'  => 'admin/jquery/jqgrid',
            'jqgrid'  => 'admin/jquery/jqgrid-min',
            'controller' => 'admin/'.$request->getController(),
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
        $head[] = "<script type='text/javascript' src='/misc/require-jquery-min.js' data-main='site'></script>";
    }



    if ( $useLess ) {
        $head[] = '<script type="text/javascript" src="/misc/less-1.3.0.min.js"></script>';
    }

    $head[] = "<meta name=\"generator\" content=\"SiteForever CMS\" />";

    return join("\n", $head);
}