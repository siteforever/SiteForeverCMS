<?php
/**
 * Контроллер баннеров
 */

class Controller_Banner extends Sfcms_Controller
{

    /**
     * Уровень доступа к действиям
     * @return array
     */
    public function access()
    {
        return array(
            'system' => array(
                'admin', 'editcat', 'delcat', 'edit', 'del', 'cat'
            ),
        );
    }


    /**
     * Инициализация
     */
    public function init()
    {
        $default = array(
            'dir' => DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'banner',
        );
        if (defined( 'MAX_FILE_SIZE' )) {
            $default[ 'max_file_size' ] = MAX_FILE_SIZE;
        }
        else {
            $default[ 'max_file_size' ] = 2 * 1024 * 1024;
        }
        $this->config->setDefault( 'banner', $default );
    }

    /**
     * Основное действие
     */
    public function indexAction()
    {
    }

    /**
     * Список категорий баннеров в админке
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->addScript('/misc/admin/banner.js');
        $this->request->setTitle( "Управление баннерами" );
        $category              = $this->getModel( 'CategoryBanner' );
        $cat_list              = $category->findAll();
        return array(
            'categories' => $cat_list,
        );
    }

    /**
     * Перенаправляет клик по баннеру на нужный сайт / страницу
     * Подсчитывает статистику
     * @return mixed
     */
    public function redirectBannerAction()
    {
        $model = $this->getModel( 'Banner' );
        $id    = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT, null );
        $id_nt = $this->request->get( 'id' );
        if (( $id_nt !== null && $id === null ) || ( !is_numeric( $id_nt ) && $id_nt !== null )) {
            $this->redirect( '/error' );
        }
        $obj                  = $model->find( $id );
        $obj[ 'count_click' ] = $obj[ 'count_click' ] + 1;
        $model->save( $obj );
        if (substr( $obj[ 'url' ], 0, 4 ) == 'http') {
            $url = $obj[ 'url' ];
        } else {
            $protocol = isset( $_SERVER[ 'SSL' ] ) ? "https://" : "http://";
            $url = $protocol . $_SERVER[ "HTTP_HOST" ] . $obj[ 'url' ];
        }
        return $this->redirect( $url );
    }

    /**
     * Сохранение категории
     * @return array|string
     */
    public function saveCatAction()
    {
        /** @var Model_CategoryBanner $model */
        $model = $this->getModel( 'CategoryBanner' );
        $form  = $model->getForm();
        $this->request->setAjax( 1, Request::TYPE_ANY );

        if( $form->getPost() ) {
            if( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                $model->save( $obj );
                return t( 'Data save successfully' );
            }
            else {
                return $form->getFeedbackString();
            }
            return 'Unkown error';
        }
        if ($edit = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT )) {
            try {
                /** @var $obj Data_Object_Banner */
                $obj = $model->find( $edit );
            }
            catch ( Exception $e ) {
                return $e->getMessage();
            }
            $form->setData( $obj->getAttributes() );
        }
        return array(
            'form'  => $form,
        );
    }

    /**
     * Удаление категории
     */
    public function delCatAction()
    {
        /** @var $model Model_CategoryBanner */
        $model = $this->getModel( 'CategoryBanner' );
        $id    = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        if ($id) {
            $model->remove( $id );
        }
        $this->redirect( 'banner/admin' );
    }

    /**
     * Удалить баннер
     */
    public function delAction()
    {
        /** @var $model Model_Banner */
        $model = $this->getModel( 'Banner' );
        $id    = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        $cat   = $model->find( $id );
        if ( $id ) {
            if ( $model->delete( $id ) ) {
                $this->request->setResponse( 'id', $id );
                $this->request->setResponseError( 0 );
            }
            else {
                $this->request->setResponseError( 1, t( 'Can not delete' ) );
            }
            $this->redirect( $this->router->createServiceLink( 'banner', 'cat', array('id'=>$cat['cat_id']) ) );
        }
    }

    /**
     * @return bool|array
     */
    public function catAction()
    {
        $this->request->addScript('/misc/admin/banner.js');
        /** @var $model Model_Banner */
        $model    = $this->getModel( 'Banner' );
        /** @var $category Model_CategoryBanner */
        $category = $this->getModel( 'CategoryBanner' );

        if ( $id = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT ) ) {
            $this->request->setTitle( 'Управление баннерами' );
            $count           = $model->count( '`cat_id`=' . $id );
            $paging          = $this->paging(
                $count, 20, $this->router->createServiceLink( 'banner', 'cat', array( 'id' => $id ) )
            );
            $crit = array(
                'where' => '`cat_id` = ?',
                'params'=> array( $id ),
                'limit' => $paging->limit,
                'order' => 'name',
            );
            $banners    = $model->findAll( $crit );
            $cat        = $category->find( $id );
            return array(
                'cat'     => $cat,
                'banners' => $banners,
                'paging'  => $paging,
            );
        }
        else {
            $this->redirect( 'banner/admin' );
            return true;
        }
    }

    /**
     * @return bool|int
     */
    public function editAction()
    {
        /**
         * @var Model_Banner $model
         */
        $model = $this->getModel( 'Banner' );
        $form  = $model->getForm();
        $this->request->setAjax( 1, Request::TYPE_ANY );
        if ($form->getPost()) {
            if ($form->validate()) {
                $obj = $model->createObject( $form->getData() );
                $model->save( $obj );
                return t( 'Data save successfully' );
            }
            else {
                return $form->getFeedbackString();
            }
        }
        if ($edit = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT )) {
            try {
                $obj = $model->find( $edit );
            }
            catch ( ControllerException $e ) {
                return $e->getMessage();
            }
            $form->setData( $obj->getAttributes() );
            $category            = $this->getModel( 'CategoryBanner' );
            $cat                 = $category->find( $obj[ 'cat_id' ] );
        }
        return array('cat'=>$cat, 'ban_name'=>$obj['cat_id'], 'form'=>$form);
    }

}
