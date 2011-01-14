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
    
    /**
     * Управление маршрутами
     * @return void
     */
    function adminAction()
    {
        /**
         * @var model_Routes $model
         * @var Data_Object_Route $route
         */
        // используем шаблон админки
        $this->request->setTitle(t('Routes'));

        $model = $this->getModel('routes');
        $routes = $model->findAll(array('order'=>'pos'), true);

        /*foreach ( $routes as $key => $route ) {
            print "$key => $route<br />";
        }*/

        // пересчет порядков
        $recount = $this->request->get('recount');
        if ( $recount == 'yes' )
        {
            $p = 0;
            foreach( $routes as $i => $r ) {
                $r['pos'] = $p;
                $p++;
                //$routes[ $i ] = $r;
            }
            //App::$db->insertUpdateMulti( $model->getTable(), $routes );
            $this->request->addFeedback(t('Order recount'));
            //redirect('admin/routes');
        }

        $routes_data    = $this->request->get('routes');

        if ( $routes_data ) {
            foreach( $routes_data as $key => $route_data ) {

                if ( $key == 0 &&
                     ( $route_data['alias'] == '' ||
                       $route_data['controller'] == '' ||
                       $route_data['action'] == '' )
                ) {
                    continue;
                }
                
                if ( $key )
                    $route  = $model->find( $key );
                else
                    $route  = $model->createObject();

                if ( $route ) {

                    if ( isset( $route_data['delete'] ) && $route->getId() ) {
                        $route->markDeleted();
                        unset( $routes[ $route->getId() ] );
                        $this->request->addFeedback(t('Delete route # ').$route_data['alias']);
                        continue;
                    }
                    
                    $route->setAttributes(array(
                        'pos'           => $route_data['pos'],
                        'alias'         => $route_data['alias'],
                        'controller'    => $route_data['controller'],
                        'action'        => $route_data['action'],
                        'active'        => isset( $route_data['active'] ) ? 1 : 0,
                        'protected'     => isset( $route_data['protected'] ) ? 1 : 0,
                        'system'        => isset( $route_data['system'] ) ? 1 : 0,
                    ));

                    if ( ! $route->getId() ) {
                        $model->save( $route );
                        $routes[ $route->getId() ]  = $route;
                    }
                }
            }
            $this->request->addFeedback(t('Data save successfully'));
            //redirect('admin/routes');
            //return;
        }
        
        $this->tpl->routes  = $routes;

        $this->request->setContent($this->tpl->fetch('system:routes.admin'));
    }
}