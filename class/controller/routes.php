<?php
/**
 * Контроллер управления маршрутами
 * @author keltanas
 */
class controller_Routes extends Controller
{
	
    function indexAction()
    {
    	
    }
    
    function init()
    {
        // пересчет порядков
        $recount = $this->request->get('recount');
        if ( $recount == 'yes' )
        {
            $router = $this->getModel('Routes');
            $routes = $router->findAll();
            $p = 0;
            foreach( $routes as $i => $r ) {
                $r['pos'] = $p;
                $p++;
                $routes[ $i ] = $r;
            }
            App::$db->insertUpdateMulti( DBROUTES, $routes );
            $this->request->addFeedback(t('Order recount'));
            //redirect('admin/routes');
        }
        
    }
    
    /**
     * Управление маршрутами
     * @return void
     */
    function adminAction()
    {
        // используем шаблон админки
        $this->request->setTitle(t('Routes'));
    	
    	$routes = $this->request->get('routes');
        
        if ( $routes ) {
            $update = array();
            foreach( $routes as $key => $r ) {
                
                foreach( $r as $k => $v ) {
                    $r[$k]  = trim($v);
                }
                
                if ( $key == 0 && ( $r['alias'] == '' || $r['controller'] == '' || $r['action'] == '' ) ) {
                    continue;
                }
                
                if ( isset( $r['delete'] ) ) {
                    App::$db->delete(DBROUTES, "id = '{$key}'");
                    $this->request->addFeedback(t('Deleted route # ').$key);
                    continue;
                }
                
                $update[] = array(
                    'id'            => $key,
                    'pos'           => $r['pos'],
                    'alias'         => $r['alias'],
                    'controller'    => $r['controller'],
                    'action'        => $r['action'],
                    'active'        => isset( $r['active'] ) ? 1 : 0,
                    'protected'     => isset( $r['protected'] ) ? 1 : 0,
                    'system'        => isset( $r['system'] ) ? 1 : 0,
                );
            }
            if ( count( $update ) && App::$db->insertUpdateMulti( DBROUTES, $update ) ) {
                $this->request->addFeedback(t('Data save successfully'));
            } else {
                $this->request->addFeedback(t('Data not saved'));
            }
            if ( $this->getAjax() ) {
                return;
            }
        }
        
        $router = $this->getModel('Routes');
        $routes = $router->findAll();

        $this->tpl->routes  = $routes;

        $this->request->setContent($this->tpl->fetch('system:routes.admin'));
    }
    	
}