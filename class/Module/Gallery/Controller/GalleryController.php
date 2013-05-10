<?php
/**
 * Контроллер галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link    http://siteforever.ru
 * @link    http://ermin.ru
 */
namespace Module\Gallery\Controller;

use Sfcms;
use Sfcms_Controller;
use Sfcms\Request;
use Exception;
use Sfcms\Form\Form;
use Module\Gallery\Object\Gallery;
use Module\Gallery\Object\Category;
use Module\Page\Object\Page;
use Module\Gallery\Model\GalleryModel;
use Module\Gallery\Model\CategoryModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GalleryController extends Sfcms_Controller
{
    public function defaults()
    {
        return array(
            'gallery',
            array(
                'dir' => '/files/gallery',
                'mime' => array('image/jpeg', 'image/gif', 'image/png'),
                'max_file_size' => defined('MAX_FILE_SIZE') ? MAX_FILE_SIZE : 2 * 1024 * 1024,
            ),
        );
    }

    /**
     * Уровень доступа к действиям
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN    => array(
                'admin', 'edit', 'list', 'delete', 'realias', 'delcat', 'editcat', 'switchimg',
            ),
        );
    }

    /**
     * Действие по-умолчанию
     * @return string|array
     */
    public function indexAction()
    {
        /**
         * @var Gallery $image
         * @var GalleryModel $model
         * @var CategoryModel $catModel
         */
//        $this->request->setTemplate( 'inner' );
        $model    = $this->getModel( 'Gallery' );
        $catModel = $this->getModel( 'GalleryCategory' );

        /*
         * Вывести изображение
         */
        if( $alias = $this->request->get( 'alias' ) ) {

            $image = $model->find( 'alias = ?', array( $alias ) );

            if( null === $image ) {
                return t( 'Image not found' );
            }

            $crit = array(
                'cond'  => 'category_id = ? AND pos > ? AND deleted != 1',
                'params'=> array( $image->category_id, $image->pos ),
                'order' => 'pos ASC',
            );

            $next = $model->find( $crit );

            $crit[ 'cond' ]  = 'category_id = ? AND pos < ? AND deleted != 1';
            $crit[ 'order' ] = 'pos DESC';

            $pred = $model->find( $crit );

            /** @var $category Category */
            $category = $catModel->find( $image->category_id );

            $bc = $this->getTpl()->getBreadcrumbs()->addPiece(null, $image->name);

            $this->request->setTitle($image->title);
            return $this->render('gallery.image', array(
                    'image' => $image,
                    'next'  => $next,
                    'pred'  => $pred,
                    'category' => $category,
                ));
        }

        $catId = $this->page->link;
        $category = null;
        if ($catId) {
            $category = $catModel->find($catId);
        }

        if ($category) {
            $crit = array(
                'cond'   => 'category_id = ? AND deleted != 1 AND hidden != 1',
                'params' => array($category->getId()),
            );

            $count = $model->count($crit['cond'], $crit['params']);

            $paging = $this->paging($count, $category->perpage, $this->page->alias);

            $crit['limit'] = $paging['limit'];
            $crit['order'] = 'pos';

            $rows = $model->findAll($crit);

            $this->tpl->assign(
                array(
                    'category' => $category,
                    'rows'     => $rows,
                    'page'     => $this->page,
                    'paging'   => $paging,
                )
            );

            return $this->tpl->fetch('gallery.category');
        }

        /**
         * Список категорий
         */
        $categories = null;

        $pageModel = $this->getModel( 'Page' );
        if( $this->page ) {
            $subPages  = $pageModel->findAll( array(
                 'condition' => ' parent = ? AND deleted != 1 ',
                 'params'    => array( $this->page->getId() ),
            ) );

            /** @var Page $obj */
            $listSubpagesIds = array();
            foreach ( $subPages as $obj ) {
                if ( $obj->get( 'link' ) && $obj->get( 'controller' ) == 'gallery' ) {
                    $listSubpagesIds[ ] = $obj->get( 'link' );
                }
            }

            if ( count( $listSubpagesIds ) ) {
                $categories = $catModel->findAll( array(
                     'condition' => ' id IN ( ' . implode( ',', $listSubpagesIds ) . ' ) ',
                ) );
            }
        }

//        $categories = $catModel->findAll();

        $this->tpl->assign( 'categories', $categories );
        return $this->tpl->fetch( 'gallery.categories' );
    }

    /**
     * Администрирование
     * @param int $editimage
     * @param string $name
     * @return mixed
     */
    public function adminAction($editimage, $name)
    {
        /**
         * @var GalleryModel $model
         * @var CategoryModel $category
         */

        $this->request->setTitle(t('Images gallery'));
        $model    = $this->getModel('Gallery');
        $category = $this->getModel('GalleryCategory');

        if ($editimage) {
            $image       = $model->find($editimage);
            $image->name = $name;

            return 'ok';
        }

        if ($this->request->get('positions')) {
            return $model->reposition();
        }

        $cat_list = $category->findAll('deleted != 1');

        return $this->render('gallery.admin', array(
            'categories' => $cat_list,
        ));
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function switchimgAction($id)
    {
        /** @var $model GalleryModel */
        $model = $this->getModel('Gallery');

        if ($id) {

            $obj    = $model->find( $id );
            $switch_result = $model->hideSwitch( $obj->getId() );
            $obj->save();

            if( $switch_result !== false ) {
                $switch_icon = '';
                if( $switch_result == 1 ) {
                    $switch_icon = Sfcms::html()->icon( 'lightbulb_off', 'Вкл' );
                } elseif( $switch_result == 2 ) {
                    $switch_icon = Sfcms::html()->icon( 'lightbulb', 'Выкл' );
                }
                return array(
                    'id'    => $id,
                    'img'   => $switch_icon,
                    'error' => 0,
                    'msg'   => '',
                );
            } else {
                return array(
                    'error' => 1,
                    'msg' =>  t( 'Switch error' ),
                );
            }
        }

    }

    /**
     * Удаление картинки
     * @param int $id
     *
     * @return array|mixed
     */
    public function deleteAction($id)
    {
        $model = $this->getModel( 'Gallery' );
        if( $id ) {
            $image = $model->find( $id );
            $image->deleted = 1;
            if( $image->save() ) {
                return array(
                    'error' => 0,
                    'msg' => t('Image was deleted'),
                    'id' => $id,
                );
            }
            return array('error' => 1, 'msg' => t( 'Can not delete' ));
        }
        return t('Image not was deleted');
    }

    /**
     * Редактирование категории
     * @param int $id
     * @return mixed
     */
    public function editcatAction($id)
    {
        /**
         * @var CategoryModel $model
         * @var Category $obj
         */
        $model = $this->getModel( 'GalleryCategory' );
        $form  = $model->getForm();

        if( $form->getPost() ) {
            if( $form->validate() ) {
                $obj    = $form->id ? $model->find($form->id) : $model->createObject();
                $obj->attributes = $form->getData();
                $model->save( $obj );
//                $this->reload( 'gallery/admin', array(), 1000 );
                return array('error'=>0,'msg'=>t( 'Data save successfully' ),'name'=>$obj->name,'id'=>$obj->id);
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }

        if ($id) {
            try {
                $obj = $model->find($id);
                $this->request->setTitle( $obj->name );
            } catch( Exception $e ) {
                return $e->getMessage();
            }

            $form->setData( $obj->getAttributes() );
            if (get_class($obj) !== 'Module\\Gallery\\Object\\Category') {
                $form->alias = $obj->getAlias();
            }
        }
        return array('form' => $form);
//        return $this->tpl->fetch( 'gallery.admin_category_edit' );
    }

    /**
     * Удалить категорию
     * @param int $id
     * @return mixed
     */
    public function delcatAction($id)
    {
        /** @var CategoryModel */
        $model = $this->getModel('GalleryCategory');
        $cat   = $model->find($id);
        if ($cat) {
            $cat->deleted = 1;
        }
        return $this->redirect('gallery/admin');
    }

    /**
     * Просмотр категории
     * @param int $id
     *
     * @return array
     */
    public function listAction($id)
    {
        $this->app()->addScript( '/misc/admin/gallery.js' );
        /** @var CategoryModel $category */
        $category = $this->getModel( 'GalleryCategory' );

        $cat = $category->find( $id );

        /** @var GalleryModel $model */
        $model = $this->getModel( 'Gallery' );

        if( isset( $_FILES[ 'image' ] ) ) {
            $this->upload( $cat );
        }

        $images = $model->findAll( array(
            'cond'  => 'category_id = :cat_id AND deleted = 0',
            'params'=> array( ':cat_id'=> $id ),
            'order' => 'pos',
        ) );

        $this->request->setTitle( $cat->name );
        return array(
            'images'   => $images,
            'category' => $cat->getAttributes(),
        );
    }


    /**
     * Редактирование картинки
     * @var GalleryModel $model
     * @return mixed
     */
    public function editAction()
    {
        $model = $this->getModel('Gallery');
        /** @var $form Form */
        $form = $this->getForm('Gallery_Image');

        /** @var Gallery $obj */
        if( $form->getPost() ) {
            if( $form->validate() ) {
                $obj = $form->id ? $model->find( $form->id ) : $model->createObject();
                $obj->attributes = $form->getData();
                $obj->save();
                return array('error' => 0,
                             'msg' => t( 'Data save successfully' ),
                             'name'=>$obj->name,
                             'id' => $obj->id,
                );
            } else {
                return array('error' => 1, 'msg' => $form->getFeedbackString());
            }
        }
        $editimg = $this->request->get( 'id' );
        if( ! isset( $obj ) ) {
            $obj = $model->find( $editimg );
        }
        $atr            = $obj->getAttributes();
        $atr[ 'alias' ] = $obj->getAlias();
        $form->setData( $atr );

        return array('form'=>$form);
    }

    /**
     * @return mixed
     */
    public function realiasAction()
    {
        $model = $this->getModel( 'Gallery' );
        $start = microtime( 1 );
        $result = array();
        try {
            $images = $model->findAll();
            /** @var Category $cat */
            /** @var Gallery $img */
            foreach( $images as $img ) {
                try {
                    $img->save();
                } catch( Exception $e ) {
                    $result[] = $e->getMessage();
                }
                $result[] = "<b>{$img->name}</b> {$img->getAlias()}";
            }
        } catch( Exception $e ) {
            return $e->getMessage();
        }
        return join('<br>', $result) . '<br>' . round( microtime( 1 ) - $start, 3 ) . ' s.';
    }

    /**
     * Загрузка файлов
     * @param Category $cat
     * @return
     */
    protected function upload( Category $cat )
    {
        /** @var GalleryModel $model */
        $model         = $this->getModel('Gallery');
        $max_file_size = $this->config->get( 'gallery.max_file_size' );
        $upload_ok     = 0;

        if ($this->request->files->has('image')) {

            $images = $this->request->files->get('image');
            $names  = array();

            if ($this->request->request->has('name')) {
                $names = $this->request->request->get('name');
            }

            $pos = $model->getNextPosition( $cat->getId() );
            $pos = $pos ? $pos : 0;


            foreach ($images as $i => $file) {
                /** @var $file UploadedFile */
                if ($file->isValid()) {
                    if (!in_array($file->getClientMimeType(), $this->config->get('gallery.mime'))) {
                        $this->request->addFeedback(t('Mime type not access in').' '.$file->getClientOriginalName());
                        continue;
                    }
                    /** @var $image Gallery */
                    $image = $model->createObject();
                    $image->pos = $pos++;
                    $image->main = 0;
                    $image->hidden = 0;
                    $image->name = $names[$i];
//                    $image->Category = $cat;
                    $image->category_id = $cat->getId();
                    $image->setUploadedFile($file);

                    $upload_ok = 1;
                } else {
                    switch($file->getError()){
                        case UPLOAD_ERR_FORM_SIZE:
                            $this->request->addFeedback( t('Form size error') );
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $this->request->addFeedback( t('Extension error') );
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $this->request->addFeedback( t('Partial error') );
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $this->request->addFeedback( t('No file') );
                            break;
                        default:
                            $this->request->addFeedback( t('Unknown error') );
                    }
                }
            }
        }
        if ($upload_ok) {
            $this->request->addFeedback(t('Images are loaded'));
        } else {
            $this->request->addFeedback(t('Image not loaded'));
        }
        return;
    }
}

