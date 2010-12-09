<?php
/**
 * Контроллер поиска
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class controller_Search extends Controller
{
    
    function indexAction()
    {
        $req = urldecode( $_SERVER['REQUEST_URI'] );
        $req = mb_convert_encoding( $req, 'UTF-8', mb_detect_encoding( $req, 'WINDOWS-1251, UTF-8' ) );
        
        //print $req;
        
        if ( preg_match('/.*sword=([a-z0-9а-я]+)/ui', $req, $match) )
        {
            $search = $match[1];
            
            //$search = preg_replace( '/[a-zа-я0-9]+/ui', '%', $search );
            
            $search_list = App::$db->fetchAll(
                "SELECT *
                FROM ".DBCATALOG."
                WHERE
                    ( name LIKE '%{$search}%'
                    OR  articul LIKE '%{$search}%' )
                    AND cat = 0 AND hidden = 0 AND deleted = 0 AND protected = 0
                ORDER BY id DESC
                LIMIT 50");
            
            $_GET['sword'] = $search;
            
            App::$tpl->assign(array(
                'sword'   => $search,
                'list'    => $search_list,
            ));
            App::$request->set(
                'tpldata.page.content',
                App::$tpl->fetch('system:search.index')
            );
        }
        else {
            App::$request->set('tpldata.page.content', 'Поиск');
        }
        
        App::$request->set('tpldata.page.title', 'Поиск');
        App::$request->set('template', 'inner');
    }
    
}