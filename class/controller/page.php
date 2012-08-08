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
                'create',
                'delete',
                'edit',
                'add',
                'correct',
                'move',
                'nameconvert',
                'save',
                'realias',
                'resort',
                'hidden',
            ),
        );
    }

    /**
     * @return mixed
     */
    public function indexAction()
    {
        /** @var $pageModel Model_Page */
        $pageModel = $this->getModel( 'Page' );
        if ( ! $this->user->hasPermission( $this->page[ 'protected' ] )) {
            throw new Sfcms_Http_Exception(t( 'Access denied' ),403);
        }

        // создаем замыкание страниц
        while ( $this->page[ 'link' ] ) {
            $page = $pageModel->find( $this->page[ 'link' ] );

            if ( ! $this->user->hasPermission( $page[ 'protected' ] ) ) {
                 throw new Sfcms_Http_Exception(t( 'Access denied' ),403);
            }
            $this->page[ 'content' ] = $page[ 'content' ];
            $this->page[ 'link' ]    = $page[ 'link' ];
        }

        if ( 'page' == $this->page->controller && $this->page[ 'id' ] != 1) {
            $subpages = $pageModel->parents[ $this->page['id'] ];
            if ( count( $subpages ) ) {
                $this->tpl->assign( 'subpages', $subpages );
                $this->tpl->assign( 'page', $this->page );
            }
        }
    }


    public function protectedAction()
    {
        $this->request->setTitle(t('Access denied'));
    }

    /**
     * Ошибка 404
     * @return void
     */
    public function error404Action()
    {
        $this->request->set( 'template', 'inner' );
        $this->request->setTitle( t('Error 404') );
        throw new Sfcms_Http_Exception(t('Page not found'), 404);
    }

    /**
     * Структура
     * @return mixed
     */
    public function adminAction()
    {
        // используем шаблон админки
        $this->request->set( 'template', 'index' );
        $this->request->setTitle( t('Site structure') );

        /** @var Model_Page $model */
        $model = $this->getModel( 'Page' );

        if ($get_link_add = $this->request->get( 'get_link_add' )) {
            $this->tpl->id = $get_link_add;
            return $this->tpl->fetch( 'system:get_link_add' );
        }

        $model->createTree();
        $model->createHtmlList();
        $this->tpl->assign('html', join( "\n", $model->html ) );
    }


    /**
     * Создает страницу
     * @return mixed
     */
    public function createAction()
    {
        /** @var $model Model_Page */
        $model = $this->getModel();
        $modules = $model->getAvaibleModules();

        $id     = $this->request->get('id', Request::INT);
        if( null === $id ) {
            return t('Unknown error');
        }

        $parent = $model->find( $id );
        return array(
            'parent' => $parent,
            'modules' => $modules,
        );
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
        $parent_id = $this->request->get( 'parent', Request::INT, 0 );
        $name      = $this->request->get( 'name' );
        $module    = $this->request->get( 'module' );

        // родительский раздел
        /** @var $parent Data_Object_Page */
        $parent = null;
        if ( $parent_id ) {
            $parent = $model->find( $parent_id );
        }

        /** @var $form Form_Form */
        $form = $model->getForm();

        $form->setData(
            array(
                'parent'    => $parent_id,
                'template'  => 'inner',
                'author'    => $this->app()->getAuth()->getId(),
                'content'   => t( 'Home page for the filling' ),
                'date'      => time(),
                'update'    => time(),
                'pos'       => $model->getNextPos( $parent_id ),
            )
        );

        $alias = $this->i18n()->translit( trim( $name, ' /' ) );
        if ( $parent && $parent->alias && 'index' != $parent->alias ) {
            $alias = trim( $parent->alias, ' /' ).'/'.$alias;
        }
        $form->getField('alias')->setValue( $alias );

        if ( $parent && $parent->sort ) {
            $form->getField('sort')->setValue( $parent->sort );
        }

        $form->getField('action')->setValue('index');
        $form->getField('name')->setValue($name);
        $form->getField('controller')->setValue($module);

        $this->request->setTitle( t('Create page') );
        $this->tpl->assign('form', $form);
        return $this->tpl->fetch( 'system:page.edit' );
    }


    /**
     * @return mixed
     */
    public function editAction()
    {
        /** @var Model_Page $model */
        $model = $this->getModel( 'Page' );
        $form  = $model->getForm();

        // идентификатор раздела, который надо редактировать
        $edit_id = $this->request->get( 'edit', Request::INT );

        if ($edit_id) {
            // данные страницы
            $page = $model->find( $edit_id );
            if ($page) {
                $form->setData( $page->getAttributes() );
                return array( 'form' => $form );
            }
        }
        return t( 'Data not valid' );
    }


    /**
     * Сохраняет данные формы
     * @return string
     */
    public function saveAction()
    {
        /** @var Model_Page $model */
        $model = $this->getModel( 'Page' );

        $form = $model->getForm();

        if ($form->getPost()) {
            if ($form->validate()) {
                /** @var $obj Data_Object_Page */
                if ( $id = $form->getField('id')->getValue() ) {
                    $obj = $model->find( $id );
                    $obj->setAttributes( $form->getData() );
                    $obj->update = time();
                } else {
                    $obj = $model->createObject( $form->getData() );
                    $obj->update = time();
                    $obj->markNew();
                }
                $this->request->setResponseError( 0, t( 'Data save successfully' ) );
            } else {
                $this->request->setResponseError( 1, $form->getFeedbackString() );
            }
            return;
        }
        $this->request->setResponseError( 1, t('Unknown error') );
    }



    public function deleteAction()
    {
        $id = $this->request->get('id');
        $page = $this->getModel()->find( $id );
        $page->set('deleted', 1);

        if ( ! $this->request->isAjax() ) {
            $this->reload('page/admin');
        }
    }

    /**
     * Resort pages
     * @return mixed
     */
    public function resortAction()
    {
        $sort = $this->request->get( 'sort' );
        if ($sort) {
            return $this->getModel()->resort( $sort );
        }
        return t('Unknown error');
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
     * Пересчитает все алиасы структуры
     * @return void
     */
    public function realiasAction()
    {
        $this->request->setTitle( 'Пересчет алиасов' );
        $pages = $this->getModel( 'Page' )->findAll( array( 'cond'=> 'deleted = 0' ) );

        $return = array();

        /** @var Data_Object_Page $page */
        foreach ( $pages as $page ) {
            try {
                $page->save();
                $return[] = 'Алиас &laquo;' . $page->name . '&raquo; &rarr; &laquo;' . $page->alias . '&raquo; пересчитан';
            } catch ( Exception $e ) {
                $return[] = 'Алиас &laquo;' . $page->name . '&raquo; &rarr; ' . $e->getMessage() . ' &laquo;' . $page->alias . '&raquo;';
            }
        }

        return '<p>Пересчет алиасов завершен</p>' . join('<br>', $return);
    }

}
