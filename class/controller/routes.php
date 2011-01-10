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
        $model  = $this->getModel('routes');
        // пересчет порядков
        $recount = $this->request->get('recount');
        if ( $recount == 'yes' )
        {
            $router = $this->getModel('Routes');
            $routes = $router->findAll(array('order'=>'pos'));
            $p = 0;
            foreach( $routes as $i => $r ) {
                $r['pos'] = $p;
                $p++;
                $routes[ $i ] = $r;
            }
            App::$db->insertUpdateMulti( $model->getTable(), $routes );
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

        $model = $this->getModel('routes');

    	$routes = $this->request->get('routes');
        
        if ( $routes ) {
            foreach( $routes as $key => $r ) {
                
                foreach( $r as $k => $v ) {
                    $r[$k]  = trim($v);
                }
                
                if ( $key == 0 && ( $r['alias'] == '' || $r['controller'] == '' || $r['action'] == '' ) ) {
                    continue;
                }
                
                if ( isset( $r['delete'] ) ) {
                    $model->delete( $key );
                    $this->request->addFeedback(t('Deleted route # ').$key);
                    continue;
                }

                $model->setData(array(
                    'id'            => $key,
                    'pos'           => $r['pos'],
                    'alias'         => $r['alias'],
                    'controller'    => $r['controller'],
                    'action'        => $r['action'],
                    'active'        => isset( $r['active'] ) ? 1 : 0,
                    'protected'     => isset( $r['protected'] ) ? 1 : 0,
                    'system'        => isset( $r['system'] ) ? 1 : 0,
                ));
                $model->save();
            }
            $this->request->addFeedback(t('Data save successfully'));
            if ( $this->getAjax() ) {
                return;
            }
        }
        
        $routes = $model->findAll(array('order'=>'pos'));

        $this->tpl->routes  = $routes;

        $this->request->setContent($this->tpl->fetch('system:routes.admin'));
    }
    	
}