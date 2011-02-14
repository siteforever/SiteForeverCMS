<?php
/**
 * Контроллер управления настройками
 * @author keltanas
 */
class controller_Settings extends Controller
{
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
        $this->request->setTitle('Настройка');

        $settings   = $this->request->get('settings');
        $settings   = ! $settings ? array() : $settings;
        
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
                    //App::$db->delete(DBSETTINGS, "id = '{$key}'");
                    $this->request->addFeedback("Удален параметр № {$key}");
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
            /*
            if ( App::$db->insertUpdateMulti( DBSETTINGS, $update ) ) {
                App::$request->addFeedback('Данные сохранены');
            } else {
                App::$request->addFeedback('Данные не были сохранены');
            }*/
        }
        
        //$model_settings = Model::getModel('Settings');
        //$settings = $router->findAll();
        
        //App::$tpl->assign('settings', $settings);
        $this->tpl->settings    = $settings;
        $this->request->setContent( $this->tpl->fetch('system:settings.admin') );
        //App::$request->set('tpldata.page.content', App::$tpl->fetch('system:settings.admin'));

    }
        
}