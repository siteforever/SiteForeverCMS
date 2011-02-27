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
    $request    = App::getInstance()->getRequest();
    $config     = App::getInstance()->getConfig();

    $head = array();
    $head[] = "<title>".$config->get('sitename').': '.$request->getTitle()."</title>";

    if ( $request->get('tpldata.page.keywords') ) {
        $head[] = "<meta name=\"keywords\" content=\"".$request->get('tpldata.page.keywords')."\" />";
    }
    if ( $request->get('tpldata.page.description') ) {
        $head[] = "<meta name=\"description\" content=\"".$request->get('tpldata.page.description')."\" />";
    }
    
    $head[] = "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />";
    $head[] = "<link title=\"\" type=\"application/rss+xml\" rel=\"alternate\" href=\"http://{$_SERVER['HTTP_HOST']}/rss\" />";

    if ( file_exists( ROOT.DS.'favicon.png' ) ) {
        $head[] = "<link rel=\"icon\" type=\"image/png\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.png\" />";
    } elseif ( file_exists( ROOT.DS.'favicon.ico' ) ) {
        $head[] = "<link rel=\"icon\" type=\"image/ico\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.ico\" />";
    }

    foreach( $request->getStyle() as $style ) {
        $head[] = "<style type=\"text/css\">@import url(\"{$style}\");</style>";
    }
    foreach( $request->getScript() as $script ) {
        $head[] = "<script type=\"text/javascript\" src=\"{$script}\"></script>";
    }

    $head[] = "<meta name=\"generator\" content=\"SiteForever CMS\" />";

    return join("\n", $head);
    //return App::$tpl->fetch('theme:head');
}