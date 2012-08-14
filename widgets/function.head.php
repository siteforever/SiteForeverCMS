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
    foreach( $app->getStyle() as $style ) {
        if ( preg_match('/.*\.css$/', $style) ) {
            $head[ ] = "<link type=\"text/css\" rel=\"stylesheet\" href=\"$style\">";
        } elseif ( preg_match('/.*\.less$/', $style) ) {
            $head[ ] = "<link type=\"text/css\" rel=\"stylesheet/less\" href=\"$style\">";
            $useLess = true;
        }
    }
    foreach( $app->getScript() as $script ) {
        $head[] = "<script type=\"text/javascript\" src=\"".$script."\"></script>";
    }

    if ( $useLess ) {
        $head[] = '<script type="text/javascript" src="/misc/less-1.3.0.min.js"></script>';
    }

    $head[] = "<meta name=\"generator\" content=\"SiteForever CMS\" />";

    return join("\n", $head);
    //return App::$tpl->fetch('theme:head');
}