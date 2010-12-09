<?php
/**
 * Контроллер управления настройками
 * @author keltanas
 */
class controller_Settings extends Controller
{
    function init()
    {
        App::$request->set('template', 'index');
    }
    
    
    function indexAction()
    {
        
    }
    
    /**
     * Управление маршрутами
     * @return void
     */
    function adminAction()
    {
        // используем шаблон админки
        App::$request->set('tpldata.page.title', 'Настройка');
        
        $settings = App::$request->get('settings');
        
        if ( $settings ) {
        	
            $update = array();
            foreach( $settings as $key => $r ) {
                
                foreach( $r as $k => $v ) {
                    $r[$k]  = trim($v);
                }
                
                if ( $key == 0 && ( $r['key'] == '' || $r['value'] == '' ) ) {
                    continue;
                }
                
                if ( isset( $r['delete'] ) ) {
                    App::$db->delete(DBSETTINGS, "id = '{$key}'");
                    App::$request->addFeedback("Удален параметр № {$key}");
                    continue;
                }
                
                $update[] = array(
                    'id'            => $key,
                    'cat'           => '0',
                    'key'           => $r['key'],
                    'value'         => $r['value'],
                    'comment'       => $r['comment'],
                    'system'        => isset( $r['system'] ) ? 1 : 0,
                );
            }
            
            if ( App::$db->insertUpdateMulti( DBSETTINGS, $update ) ) {
                App::$request->addFeedback('Данные сохранены');
            } else {
                App::$request->addFeedback('Данные не были сохранены');
            }
        }
        
        $router = Model::getModel('model_Settings');
        $settings = $router->findAll();
        
        App::$tpl->assign('settings', $settings);
        
        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:settings.admin'));
    }
        
}