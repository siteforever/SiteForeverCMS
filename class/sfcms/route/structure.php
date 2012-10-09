<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use App;
use Sfcms\Route;
use Sfcms\Module;
use Data_Object_Page;

class Structure extends Route
{
    protected static $_aliases = null;

    /**
     * Заполняет таблицу алиасов, если она отсутствует
     */
    protected function callAliases()
    {
        if ( null === self::$_aliases ) {
            /** @var $model \Model_Page */
            $model = \Sfcms_Model::getModel( 'Page' );
            $pages = $model->getAll();
            foreach ( $pages as $page ) {
                self::$_aliases[ $page->alias ] = $page;
            }
        }
    }

    /**
     * @param $route
     * @return mixed
     */
    public function route( $route )
    {
        $alias = null;
        $this->callAliases();
        /** @var $page Data_Object_Page */
        do {
            if ( isset( self::$_aliases[ $route ] ) ) {
                if ( null !== $alias ) {
                    App::getInstance()->getRequest()->set('alias', $alias);
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
        $className = Module::getModuleClass( $page->controller );
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
