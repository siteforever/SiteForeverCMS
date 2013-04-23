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

class GalleryController extends Sfcms_Controller
{
    public function defaults()
    {
        return array(
            'gallery',
            array(
                'dir' => DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'gallery',
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
            'system'    => array(
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

            $this->getTpl()->assign('image', $image);
            $this->getTpl()->assign('next', $next);
            $this->getTpl()->assign('pred', $pred);
            $this->getTpl()->assign('category', $category);

            $bc = $this->getTpl()->getBreadcrumbs();
            $bc->addPiece( null, $image->name );

            $this->request->setTitle( $image->title );
            return $this->tpl->fetch( 'gallery.image' );
        }

        $catId = $this->page->link;
        $category = null;
        if ( $catId ) {
            $category = $catModel->find( $catId );
        }

        if ( $category ) {
            $crit = array(
                'cond'      => 'category_id = ? AND deleted != 1 AND hidden != 1',
                'params'    => array( $category->getId() ),
            );

            $count = $model->count( $crit[ 'cond' ], $crit[ 'params' ] );

            $paging = $this->paging( $count, $category->perpage, $this->page->alias );

            $crit[ 'limit' ] = $paging[ 'limit' ];
            $crit[ 'order' ] = 'pos';

            $rows = $model->findAll( $crit );

            $this->tpl->assign( array(
                'category' => $category,
                'rows' => $rows,
                'page' => $this->page,
                'paging' => $paging,
            ));

            return $this->tpl->fetch( 'gallery.category' );
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

        $this->request->setTitle( t( 'Images gallery' ) );

        $model = $this->getModel( 'Gallery' );

        $category = $this->getModel( 'GalleryCategory' );

        if ( $editimage ) {
            $image = $model->find( $editimage );
            $image->name = $name;
            return 'ok';
        }

        if( $this->request->get( 'positions' ) ) {
            return $model->reposition();
        }

        $cat_list = $category->findAll('deleted != 1');

        $this->tpl->categories = $cat_list;
//        $this->request->setContent( $this->tpl->fetch( 'gallery.admin_category' ) );
//        return 1;
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
            if( get_class( $obj ) !== '\Module\Gallery\Object\Category' ) {
                $form->alias = $obj->getAlias();
            }
        }
        $this->tpl->form = $form;
//        return $this->tpl->fetch( 'system:gallery.admin_category_edit' );
    }

    /**
     * Удалить категорию
     * @param int $id
     * @return mixed
     */
    public function delcatAction($id)
    {
        /** @var CategoryModel */
        $model = $this->getModel( 'GalleryCategory' );
        //        $id = $this->request->get('delcat', FILTER_SANITIZE_NUMBER_INT);
        $cat = $model->find( $id );
        if ( $cat ) {
            $cat->deleted = 1;
            $cat->save();
        }
//        if( $id ) {
//            $model->delete( $id );
//        }
        return $this->redirect( 'gallery/admin' );
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
        $model = $this->getModel( 'Gallery' );
        /** @var $form Form */
        $form = $this->getForm( 'gallery_image' );

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

        if( isset( $_FILES[ 'image' ] ) && is_array( $_FILES[ 'image' ] ) ) {
            $images = $_FILES[ 'image' ];
            $names  = array();

            if( $this->request->get( 'name' ) ) {
                $names = $this->request->get( 'name' );
            }

            $pos = $model->getNextPosition( $cat->getId() );
            $pos = $pos ? $pos : 0;


            foreach( $images[ 'error' ] as $i => $err ) {
                switch ( $err ) {
                    case UPLOAD_ERR_OK:
                        /** @var $image Gallery */
                        $image = $model->createObject( array(
                                                    'pos'   => $pos,
                                                    'main'  => '0',
                                                    'hidden'=> '0',
                                                ) );
                        $pos ++;
                        if( $images[ 'size' ][ $i ] <= $max_file_size
                            && in_array( $images[ 'type' ][ $i ], array( 'image/jpeg', 'image/gif', 'image/png' ) )
                        ) {
                            $upload_ok = 1;
                            $dest      = $this->config->get( 'gallery.dir' ) . DIRECTORY_SEPARATOR . substr(
                                '0000' . $cat->getId(), - 4, 4 );
                            if( ! is_dir( ROOT . $dest ) ) {
                                mkdir( ROOT . $dest, 0777, true );
                            }
                            $src                = $images[ 'tmp_name' ][ $i ];
                            $image->category_id = $cat->getId();
                            if( isset( $names[ $i ] ) ) {
                                $image->name = $names[ $i ];
                            }
                            $model->save( $image );
                            $g_id         = $image->getId();
                            $img          = $dest . DIRECTORY_SEPARATOR . $g_id . '_' . $images[ 'name' ][ $i ];
                            if( move_uploaded_file( $src, ROOT . $img ) ) {
                                $image->image = str_replace( DIRECTORY_SEPARATOR, '/', $img );
                            }
                            $image->save();
                            if ( 0 == $image->pos ) {
                                $cat->image = $image->image;
                                $cat->save();
                            }
                        } else {
                            $this->request->addFeedback(
                                "Exceeds the maximum size {$images['size'][$i]} from $max_file_size"
                            );
                        }
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->request->addFeedback( 'form size error' );
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $this->request->addFeedback( 'extension error' );
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->request->addFeedback( 'partial error' );
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->request->addFeedback( 'no file' );
                        break;
                    default:
                        $this->request->addFeedback( 'unknown error' );
                }
            }
        }
        if( $upload_ok ) {
            $this->request->addFeedback( t( 'Images are loaded' ) );
        } else {
            $this->request->addFeedback( t( 'Image not loaded' ) );
        }
        return;
    }
}

