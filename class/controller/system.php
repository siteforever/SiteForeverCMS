<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
class controller_System extends Controller
{
    function indexAction()
    {
        App::$request->set('tpldata.page.name', 'System config');
        App::$request->set('tpldata.page.title', 'Конфигурация системы');
        App::$request->set('tpldata.page.template', 'index');
        
        $sys = ini_get_all();
        
        foreach( $sys as $key => $value ) {
            if ( strpos( $key, '.' ) ) {
                unset( $sys[ $key ] );
                $key = str_replace('.', '_', $key);
                $sys[ $key ] = $value;
            }
        }
        
        App::$tpl->assign('sys', $sys);
        
        //printVar( $sys );
        
        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:system.index'));
    }
}