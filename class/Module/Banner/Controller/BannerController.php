<?php
/**
 * Контроллер баннеров
 */
namespace Module\Banner\Contoller;

use Sfcms_Controller;
use Model_Banner;
use Data_Object_Banner;
use Model_CategoryBanner;
use Sfcms\Exception;

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
                'admin', 'editcat', 'delcat', 'edit', 'del', 'cat', 'save'
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
     * Список категорий баннеров в админке
     * @return mixed
     */
    public function adminAction()
    {
        $this->app()->addScript('/misc/admin/banner.js');
        $this->request->setTitle( t('Banners category list') );
        $category              = $this->getModel( 'CategoryBanner' );
        $cat_list              = $category->findAll();
        return array(
            'categories' => $cat_list,
        );
    }

    /**
     * Перенаправляет клик по баннеру на нужный сайт / страницу
     * Подсчитывает статистику
     * @param int $id
     * @return mixed
     */
    public function redirectBannerAction( $id )
    {
        /** @var $model Model_Banner */
        $model = $this->getModel( 'Banner' );
        if ( ! $id ) {
            return $this->redirect( $this->router->createLink('error') );
        }
        /** @var $obj Data_Object_Banner */
        $obj = $model->find( $id );
        $obj->count_click++;
        return $this->redirect( $obj->url );
    }

    /**
     * Сохранение категории
     * @param int $id
     * @return array|string
     */
    public function saveCatAction( $id )
    {
        /** @var Model_CategoryBanner $model */
        $model = $this->getModel( 'CategoryBanner' );
        $form  = $model->getForm();

        if( $form->getPost() ) {
            if( $form->validate() ) {
                $obj = $form['id'] ? $model->find( $form['id'] ) : $model->createObject();
                $obj->attributes = $form->getData();
                return array('error'=>0,'msg'=> t( 'Data save successfully' ));
            } else {
                return array('error'=>1,'msg'=> $form->getFeedbackString());
            }
        }
        if ( $id ) {
            try {
                /** @var $obj Data_Object_Banner */
                $obj = $model->find( $id );
            } catch ( Exception $e ) {
                return $e->getMessage();
            }
            $form->setData( $obj->attributes );
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
        return $this->redirect( $this->router->createServiceLink('banner','admin') );
    }

    /**
     * Удалить баннер
     * @param int $id
     * @return array
     */
    public function delAction( $id )
    {
        /** @var $model Model_Banner */
        $model = $this->getModel( 'Banner' );
        $cat   = $model->find( $id );
        if ( $cat ) {
            if ( $model->delete( $id ) ) {
                $this->request->setResponse( 'id', $id );
                return $this->request->setResponseError( 0, t( 'Delete successfully' ) );
            } else {
                return $this->request->setResponseError( 1, t( 'Can not delete' ) );
            }
            //return $this->redirect( $this->router->createServiceLink( 'banner', 'cat'), array('id'=>$cat['cat_id'] ) );
        }
        throw new Exception('Category not found');
    }

    /**
     * @param int $id
     * @return bool|array
     */
    public function catAction( $id )
    {
        $this->app()->addScript('/misc/admin/banner.js');
        /** @var $model Model_Banner */
        $model    = $this->getModel( 'Banner' );
        /** @var $category Model_CategoryBanner */
        $category = $this->getModel( 'CategoryBanner' );

        if ( $id ) {
            $crit = array(
                'where' => '`cat_id` = ?',
                'params'=> array( $id ),
            );
            $count           = $model->count( $crit['where'], $crit['params'] );
            $paging          = $this->paging(
                $count, 20, $this->router->createServiceLink( 'banner', 'cat', array( 'id' => $id ) )
            );

            $crit['limit'] = $paging->limit;
            $crit['order'] = 'name';

            $banners    = $model->findAll( $crit );
            $cat        = $category->find( $id );

            $this->request->setTitle( 'Управление баннерами' );

            return array(
                'cat'     => $cat,
                'banners' => $banners,
                'paging'  => $paging,
            );
        }
        return $this->redirect( 'banner/admin' );
    }

    /**
     * @return bool|int
     */
    public function editAction()
    {
        /** @var Model_Banner $model */
        $model = $this->getModel( 'Banner' );
        $form  = $model->getForm();
        $this->request->setAjax( 1, Request::TYPE_ANY );
        if ( $id = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT ) ) {
            try {
                $obj = $model->find( $id );
            }
            catch ( Sfcms_Model_Exception $e ) {
                return $e->getMessage();
            }
            $categoryModel   = $this->getModel( 'CategoryBanner' );
            $cat             = $categoryModel->find( $obj[ 'cat_id' ] );
            $form->setData( $obj->getAttributes() );
            $form->getField( 'cat_id' )->setValue( $cat->getId() );
            return array( 'cat'     => $cat,
                          'form'    => $form
            );
        }
        if ( ! $cat_id = $this->request->get('cat') ) {
            return 'error';
        }
        $cat = $this->getModel('CategoryBanner')->find( $cat_id );
        $form->getField( 'cat_id' )->setValue( $cat->getId() );
        return array( 'cat'     => $cat,
                      'form'    => $form
        );
    }


    /**
     * Сохранение баннера
     * @return string|void
     */
    public function saveAction()
    {
        /** @var Model_Banner $model */
        $model = $this->getModel( 'Banner' );
        $form  = $model->getForm();
        if ($form->getPost()) {
            if ($form->validate()) {
                $obj = $form['id'] ? $model->find( $form['id'] ) : $model->createObject();
                $obj->attributes = $form->getData();
                return array('error'=>0,'msg'=>t( 'Data save successfully' ) );
            } else {
                return array('error'=>1, 'msg'=> $form->getFeedbackString() );
            }
        }
        return 'error';
    }

}
