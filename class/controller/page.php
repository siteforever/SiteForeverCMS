<?php
/**
 * Контроллер страниц
 * @author keltanas <nikolay@ermin.ru>
 */

class Controller_Page extends Sfcms_Controller
{
    public function access()
    {
        return array(
            'system'    => array(
                'admin',
                'edit',
                'add',
                'correct',
                'move',
                'nameconvert',
                'save',
                'realias',
                'hidden'
            ),
        );
    }

    /**
     * @return
     */
    public function indexAction()
    {
        /** @var $page_model Model_Page */
        $page_model = $this->getModel( 'Page' );
        if (!$this->user->hasPermission( $this->page[ 'protected' ] )) {
            $this->request->setContent( t( 'Access denied' ) );
            return;
        }

        App::$DEBUG && $this->app()->getLogger()->log( $this->page, 'page' );

        // создаем замыкание страниц
        while ( $this->page[ 'link' ] ) {
            $page = $page_model->find( $this->page[ 'link' ] );

            if (!$this->user->hasPermission( $page[ 'protected' ] )) {
                $this->request->setContent( t( 'Access denied' ) );
                return;
            }
            $this->page[ 'content' ] = $page[ 'content' ];
            $this->page[ 'link' ]    = $page[ 'link' ];
        }

        if ($this->page[ 'controller' ] == 'page' && $this->page[ 'id' ] != 1) {
            $subpages = $page_model->findAll(
                array(
                    'condition' => ' parent = ? AND hidden = 0 AND deleted = 0 ',
                    'params'    => array( $this->page[ 'id' ] ),
                    'order'     => 'pos',
                )
            );

            if ($subpages) {
                $this->tpl->assign( 'subpages', $subpages );
                $this->tpl->assign( 'page', $this->page );
                $this->request->setContent( $this->tpl->fetch( 'page.index' ) );
            }
        }
    }

    /**
     * Ошибка 404
     * @return void
     */
    public function errorAction()
    {
        $this->request->set( 'template', 'inner' );
        $this->request->setTitle( 'Ошибка 404. Страница не найдена' );
        $this->request->setContent( 'Ошибка 404.<br />Страница не найдена.' );
    }

    /**
     * Структура
     * @return void
     */
    public function adminAction()
    {
        // используем шаблон админки
        $this->request->set( 'template', 'index' );
        $this->request->setTitle( 'Управление сайтом' );

        /**
         * @var Model_Page $model
         */
        $model = $this->getModel( 'Page' );

        // обновление
        if ($this->request->get( 'up' ) || $this->request->get( 'down' )) {
            return $this->moveAction();
        }

        if ($get_link_add = $this->request->get( 'get_link_add' )) {
            $this->tpl->id = $get_link_add;
            die( $this->tpl->fetch( 'system:get_link_add' ) );
        }

        // проверка на правильность алиаса
        if ($test_alias = $this->request->get( 'test_alias' )) {
            if ($model->findByRoute( $test_alias )) {
                die( '0' );
            } else {
                die( 'yes' );
            }
        }

        $this->request->setTitle( 'Структура сайта' );

        $sort = $this->request->get( 'sort' );
        if ($sort) {
            return $model->resort( $sort );
        }

        $do   = $this->request->get( 'do' );
        $part = $this->request->get( 'part' );

        if ($do && $part) {
            $model->switching( $do, $part );
            return $this->redirect( 'admin' );
        }

        $model->createTree();

        //printVar($model->parents);
        $model->createHtmlList();

        $this->tpl->html = join( "\n", $model->html );
        $this->request->setContent( $this->tpl->fetch( 'system:page.admin' ) );
    }

    /**
     * @return void
     */
    public function nameconvertAction()
    {
        $this->request->setTemplate( 'inner' );
        $this->request->setContent( __METHOD__ );
    }

    /**
     * Добавления
     * @return mixed
     */
    public function addAction()
    {
        /**
         * @var Model_Page $model
         */
        $model = $this->getModel( 'Page' );

        // идентификатор раздела, в который надо добавить
        $parent_id = $this->request->get( 'add' );

        // родительский раздел
        if ($parent_id) {
            $parent = $model->find( $parent_id );
        }
        else {
            $parent = $model->createObject(
                array(
                    'controller'    => 'page',
                    'action'        => 'index',
                    'sort'          => 'pos',
                )
            );
        }

        $form = $model->getForm();

        $form->setData(
            array(
                'parent'    => $parent_id,
                'template'    => 'inner',
                'author'    => '1',
                'content'   => t( 'Home page for the filling' ),
                'date'      => time(),
                'update'    => time(),
                'pos'       => $model->getNextPos( $parent_id ),
            )
        );

        if (isset( $parent[ 'alias' ] )) {
            $form->alias = trim( $parent[ 'alias' ], ' /' );
        }

        if (isset( $parent[ 'controller' ] )) {
            $form->controller = $parent[ 'controller' ];
        }

        if (isset( $parent[ 'action' ] )) {
            $form->action = $parent[ 'action' ];
        }

        if (isset( $parent[ 'sort' ] )) {
            $form->sort = $parent[ 'sort' ];
        }

        $this->request->setTitle( 'Добавить страницу' );
        $this->tpl->assign('form', $form);
        return $this->tpl->fetch( 'system:page.edit' );
    }

    /**
     * Сохраняет данные формы
     * @return string
     */
    public function saveAction()
    {
        /**
         * @var Model_Page $model
         */
        $model = $this->getModel( 'Page' );

        $form = $model->getForm();

        if ($form->getPost()) {
            if ($form->validate()) {
                $form->update = time();
                $obj          = $model->createObject( $form->getData() );

                $old_id = $obj->getId();

                try {
                    if ( $obj->save() ) {
                        $this->reload( '', array( 'controller'=> 'page', 'action'    => 'admin' ) );
                        return t( 'Data save successfully' );
                    } else {
                        return t( 'Data not saved' );
                    }
                }
                catch ( Sfcms_Model_Exception $e ) {
                    return $e->getMessage();
                }
            }
            else {
                return $form->getFeedback();
            }
        }
    }

    /**
     * @return mixin
     */
    public function editAction()
    {
        /**
         * @var Model_Page $model
         */
        $model = $this->getModel( 'Page' );

        $form = $model->getForm();

        // используем шаблон админки
        $this->request->set( 'template', 'index' );
        $this->request->setTitle( 'Управление сайтом' );

        // идентификатор раздела, который надо редактировать
        $edit_id = $this->request->get( 'edit', FILTER_SANITIZE_NUMBER_INT );

        // идентификатор раздела, в который надо добавить
        $add_id = $this->request->get( 'add', FILTER_SANITIZE_NUMBER_INT );

        // родительский раздел
        if ($add_id) {
            $parent = $model->find( $add_id );
        }

        if ($edit_id) {
            // данные раздела
            $part = $model->find( $edit_id );

            if ($part) {
                $form->setData( $part->getAttributes() );
            }

            return array( 'form' => $form );
//            $this->request->setContent( $this->tpl->fetch( 'system:page.edit' ) );
        }

        return t( 'Data not valid' );
    }

    /**
     * Меняет св-во hidden у страницы
     */
    public function hiddenAction()
    {
        $id   = $this->request->get( 'id' );
        $page = $this->getModel( 'Page' )->find( $id );

        $page->set( 'hidden', 0 == $page->get( 'hidden' ) ? 1 : 0 );

        $page->save();

        $this->request->setContent(
            $this->getModel( 'Page' )->getOrderHidden( $id, $page->get( 'hidden' ) )
        );
    }

    /**
     * Перемещение раздела
     * @return void
     */
    public function moveAction()
    {
        // используем шаблон админки
        $this->request->set( 'template', 'index' );
        $this->request->setTitle( 'Управление сайтом' );

        $model = $this->getModel( 'Page' );
        if ($up = $this->request->get( 'up' )) {
            $model->moveUp( $up );
        }
        if ($down = $this->request->get( 'down' )) {
            $model->moveDown( $down );
        }
        $this->reload( 'admin' );
    }

    /**
     * Пересчитает все алиасы структуры
     * @return void
     */
    public function realiasAction()
    {
        $this->request->setTitle( 'Пересчет алиасов' );
        $pages = $this->getModel( 'Page' )->findAll( array( 'cond'=> 'deleted = 0' ) );
        ob_implicit_flush( 1 );

        /**
         * @var Data_Object_Page $page
         */
        foreach ( $pages as $page ) {
            try {
                $page->save();
                print( 'Алиас &laquo;' . $page->alias . '&raquo; пересчитан<br />' );
                //$this->request->addFeedback('Алиас &laquo;' . $page->alias .'&raquo; пересчитан');
            }
            catch ( Exception $e ) {
                print( $e->getMessage() . ' &laquo;' . $page->alias . '&raquo;<br />' );
                //$this->request->addFeedback( $e->getMessage() . ' &laquo;' . $page->alias .'&raquo;' );
            }
        }

        $this->request->setContent( 'Пересчет алиасов завершен' );
    }

}
