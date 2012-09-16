<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use Sfcms\Route as Route;

class Structure extends Route
{
    protected static $_aliases = array();

    public function __construct()
    {
        /** @var $model \Model_Page */
        $model = \Sfcms_Model::getModel( 'Page' );
        $pages = $model->all;
        foreach ( $pages as $page ) {
            self::$_aliases[ $page->alias ] = $page;
        }
    }


    /**
     * @param $route
     * @return mixed
     */
    public function route( $route )
    {
        $alias = null;
        /** @var $page \Data_Object_Page */
        do {
//            \App::getInstance()->getLogger()->log($alias, 'alias');
            if ( isset( self::$_aliases[ $route ] ) ) {
                if ( null !== $alias ) {
                    \App::getInstance()->getRequest()->set('alias', $alias);
                }
                return $this->getPageState( self::$_aliases[ $route ] );
            }
            $arRoute = explode('/', $route);
            $alias = array_pop( $arRoute );
            $route = implode('/',$arRoute);
        } while( $route );
        return false;
    }

    /**
     * @param \Data_Object_Page $page
     *
     * @return array
     */
    private function getPageState( \Data_Object_Page $page )
    {
        $className = \Sfcms\Module::getModuleClass( $page->controller );
        $field = $className::relatedField();
        $id = $page->get( $field );
        return array(
            'controller' => $page->controller,
            'action'     => $page->action,
            'params'     => array(
                'pageid'     => $id,
                'template'   => $page->template,
                'system'     => $page->system,
            ),
        );
    }

}
