<?php
/**
 * Контроллер баннеров
 */
namespace Module\Banner\Controller;

use Sfcms\Controller;
use Sfcms\Request;
use Module\Banner\Model\BannerModel;
use Module\Banner\Object\Banner;
use Module\Banner\Model\CategoryModel;
use Sfcms\Exception;
use Symfony\Component\HttpFoundation\Response;

class BannerController extends Controller
{
    /**
     * Уровень доступа к действиям
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN => array(
                'admin', 'editcat', 'delcat', 'edit', 'del', 'cat', 'save'
            ),
        );
    }


    /**
     * Инициализация
     */
    public function init()
    {
//        $default = array(
//            'dir' => DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'banner',
//        );
//        if (defined('MAX_FILE_SIZE')) {
//            $default['max_file_size'] = MAX_FILE_SIZE;
//        } else {
//            $default['max_file_size'] = 2 * 1024 * 1024;
//        }
//        $this->config->setDefault('banner', $default);
    }

    /**
     * Список категорий баннеров в админке
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->setTitle('Banners category list');
        $category = $this->getModel('CategoryBanner');
        $cat_list = $category->findAll();

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
    public function redirectBannerAction($id)
    {
        /** @var $model BannerModel */
        $model = $this->getModel('Banner');
        if (!$id) {
            return $this->redirect($this->router->createLink('error'));
        }
        /** @var $obj Banner */
        $obj = $model->find($id);
        $obj->count_click++;
        return $this->redirect($obj->url);
    }

    /**
     * Сохранение категории
     * @param int $id
     * @return array|string
     */
    public function saveCatAction($id)
    {
        /** @var CategoryModel $model */
        $model = $this->getModel('CategoryBanner');
        $form  = $model->getForm();

        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $obj = $form['id'] ? $model->find($form['id']) : $model->createObject()->markNew();
                $obj->attributes = $form->getData();

                return $this->renderJson(array('error' => 0, 'msg' => $this->t('Data save successfully')));
            } else {
                return $this->renderJson(array('error' => 1, 'msg' => $form->getFeedbackString()));
            }
        }
        if ( $id ) {
            try {
                /** @var $obj Banner */
                $obj = $model->find($id);
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
     * @param int $id
     *
     * @return Response
     */
    public function delCatAction($id)
    {
        /** @var $model CategoryModel */
        $model = $this->getModel('CategoryBanner');
        if ($id) {
            $model->remove($id);
        }

        return $this->redirect($this->router->createServiceLink('banner', 'admin'));
    }

    /**
     * Удалить баннер
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    public function delAction($id)
    {
        /** @var $model BannerModel */
        $model = $this->getModel('Banner');
        /** @var $banner Banner */
        $banner = $model->find($id);
        if ($banner) {
            $banner->deleted = 1;
            return $this->renderJson(array('id'=>$id, 'error'=>0, 'msg'=>$this->t('Delete successfully')));
        }
        throw new Exception('Category not found');
    }

    /**
     * @param int $id
     * @return bool|array
     */
    public function catAction( $id )
    {
        /** @var $model BannerModel */
        $model    = $this->getModel( 'Banner' );
        /** @var $category CategoryModel */
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
     * @throws \Sfcms_Http_Exception
     */
    public function editAction()
    {
        $id = $this->request->get('id');
        /** @var BannerModel $model */
        $model = $this->getModel('Banner');
        $form  = $model->getForm();
        if ($id) {
            /** @var $obj Banner */
            $obj = $model->find($id);
            if (!$obj) {
                throw new \Sfcms_Http_Exception('Banner not found', 404);
            }
            $categoryModel = $this->getModel('CategoryBanner');
            $cat           = $categoryModel->find($obj->cat_id);
            $form->setData($obj->getAttributes());
            $form->cat_id = $cat->getId();

            return array(
                'cat'  => $cat,
                'form' => $form
            );
        }
        if (!$cat_id = $this->request->get('cat')) {
            return 'error';
        }
        $cat = $this->getModel('CategoryBanner')->find($cat_id);
        $form->cat_id = $cat->getId();

        return array(
            'cat'  => $cat,
            'form' => $form
        );
    }


    /**
     * Сохранение баннера
     * @return string|void
     */
    public function saveAction()
    {
        /** @var BannerModel $model */
        $model = $this->getModel('Banner');
        $form  = $model->getForm();
        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $obj = $form['id'] ? $model->find($form['id']) : $model->createObject()->markNew();
                $obj->attributes = $form->getData();

                return $this->renderJson(array('error' => 0, 'msg' => $this->t('Data save successfully')));
            } else {
                return $this->renderJson(array('error' => 1, 'msg' => $form->getFeedbackString()));
            }
        }
        return $this->renderJson(array('error'=>1));
    }

}
