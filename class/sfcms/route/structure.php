<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;

class Structure extends \Sfcms\Route
{
    /**
     * @param $route
     * @return mixed
     */
    public function route( $route )
    {
        /** @var $model \Model_Page */
        $model = \Sfcms_Model::getModel( 'Page' );

        $pages = $model->all;
        $alias = null;

        /** @var $page \Data_Object_Page */
        do {
            foreach ( $pages as $page ) {
                if ( 0 === strcasecmp( $page->alias, $route ) ) {
                    if ( null !== $alias ) {
                        \App::getInstance()->getRequest()->set('alias', $alias);
                    }
                    return $this->getPageState( $page );
                }
            }
            $route = explode('/', $route);
            $alias = array_pop( $route );
            $route = implode('/',$route);
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
        if ( in_array( $page->controller, array('page','guestbook','gallery') ) ) {
            $id = $page->id;
        } else {
            $id = $page->link;
        }
        return array(
            'controller' => $page->controller,
            'action'     => $page->action,
            'params'     => array(
                'id'         => $id,
                'template'   => $page->template,
                'system'     => $page->system,
            ),
        );
    }

}
