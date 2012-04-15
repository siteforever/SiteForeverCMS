<?php
/**
 * Контроллер галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link    http://siteforever.ru
 * @link    http://ermin.ru
 */
class Controller_Gallery extends Sfcms_Controller
{

    public function init()
    {
        $default = array(
            'dir'           => DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'gallery',
            'thumb_prefix'  => 'thumb_',
            'middle_prefix' => 'middle_',
        );
        if( defined( 'MAX_FILE_SIZE' ) ) {
            $default[ 'max_file_size' ] = MAX_FILE_SIZE;
        } else {
            $default[ 'max_file_size' ] = 2 * 1024 * 1024;
        }
        $this->config->setDefault( 'gallery', $default );
    }

    /**
     * Уровень доступа к действиям
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
                'admin', 'edit', 'delete', 'deleteImage', 'realias', 'viewcat', 'editimage', 'delcat', 'editcat',
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
         * @var Data_Object_Gallery $image
         * @var model_gallery $model
         * @var model_galleryCategory $catModel
         */
        $this->request->setTemplate( 'inner' );
        $model          = $this->getModel( 'Gallery' );
        $catModel = $this->getModel( 'GalleryCategory' );

        /*
         * Вывести изображение
         */
        if( $img = $this->request->get( 'img', Request::INT ) ) {
            $image = $model->find( $img );

            if( null !== $image ) {

                $crit = array(
                    'cond'  => 'category_id = ? AND pos > ?',
                    'params'=> array( $image->category_id, $image->pos ),
                    'order' => 'pos ASC',
                    'limit' => '1',
                );

                $next = $model->find( $crit );

                $crit[ 'cond' ]  = 'category_id = ? AND pos < ?';
                $crit[ 'order' ] = 'pos DESC';

                $pred = $model->find( $crit );

                $category = $catModel->find( $image->category_id );

                $this->tpl->image    = $image;
                $this->tpl->next     = $next;
                $this->tpl->pred     = $pred;
                $this->tpl->category = $category;

                $bc = $this->tpl->getBreadcrumbs();
                $bc->clearPieces();
                $bc->addPiece( 'index', 'Главная' );
                $bc->addPiece( $category->getAlias(), $category->name );
                $bc->addPiece( null, $image->name );

                $title = $image->meta_title ? $image->meta_title : $category->name . ' - ' . $image->name;
                //                $h1       = $image->meta_h1 ? $image->meta_h1 : $category->name . ' - ' . $image->name;
                $h1                 = $image->meta_h1 ? $image->meta_h1 : $title;
                $this->tpl->meta_h1 = $h1;

                $description = $image->meta_description ? $image->meta_description : null;
                $keywords    = $image->meta_keywords ? $image->meta_keywords : null;
                if( $description ) {
                    $this->request->set( 'tpldata.page.description', str_random_replace( $h1, $description ) );
                }
                if( $keywords ) {
                    $this->request->set( 'tpldata.page.keywords', str_random_replace( $h1, $keywords ) );
                }

                //                $this->request->setTitle( $category->name . ' &rarr; ' . $image->name );
                $this->request->setTitle( $title );

                return $this->tpl->fetch( 'gallery.image' );
            } else {
                return t( 'Image not found' );
            }
        }

        /**
         * Вывести категорию
         */
        $cat_id = $this->request->get( 'cat', FILTER_SANITIZE_NUMBER_INT, $this->page[ 'link' ] );
        if( $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT ) ) {
            $cat_id = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT, $this->page[ 'link' ] );
        }

        if( null === $cat_id ) {
            return 'Не указан идентификатор категории';
        }

        $category = null;
        if ( $cat_id ) {
            $category = $catModel->find( $cat_id );
        }

        if ( $category ) {

            $crit = array(
                'cond'      => 'hidden = 0 AND category_id = ?',
                'params'    => array( $category->getId() ),
            );

            $count = $model->count( $crit[ 'cond' ], $crit[ 'params' ] );

            $paging = $this->paging( $count, $category->perpage, $this->page[ 'alias' ] . '/cat=' . $category[ 'id' ] );

            $crit[ 'limit' ] = $paging[ 'limit' ];
            $crit[ 'order' ] = 'pos';

            $rows = $model->findAll( $crit );


            $this->tpl->assign( array(
                'category' => $category,
                'rows' => $rows,
                'page' => $this->page,
                'paging' => $paging,
            ));

            $title = $category->meta_title ? $category->meta_title : $category->name;
            //            $h1       = $category->meta_h1 ? $category->meta_h1 : $category->name;
            $h1 = $category->meta_h1 ? $category->meta_h1 : $title;

            $description = $category->meta_description ? $category->meta_description : '';
            $keywords    = $category->meta_keywords ? $category->meta_keywords : '';


            $this->tpl->meta_h1 = $h1;

            if( $description ) {
                $this->request->set( 'tpldata.page.description', str_random_replace( $h1, $description ) );
            }
            if( $keywords ) {
                $this->request->set( 'tpldata.page.keywords', str_random_replace( $h1, $keywords ) );
            }

            $bc = $this->tpl->getBreadcrumbs();
            //            $bc->addPiece('index', 'Главная');
            $bc->addPiece( $this->router->createServiceLink( 'gallery', 'index', array( 'id'=> $cat_id ) ), $category->name );

            $this->request->setTitle( $title );
            return $this->tpl->fetch( 'gallery.category' );
        }

        /**
         * Список категорий
         */
//        $page_model = $this->getModel( 'Page' );
//        $sub_pages  = $page_model->findAll( array(
//             'condition' => ' parent = ? AND deleted = 0 ',
//             'params'    => array( $this->page[ 'id' ] ),
//        ) );

        /** @var Data_Object_Page $obj */
//        $list_page_id = array();
//        foreach ( $sub_pages as $obj ) {
//            if ( $obj->get( 'link' ) && $obj->get( 'controller' ) == 'gallery' ) {
//                $list_page_id[ ] = $obj->get( 'link' );
//            }
//        }


//        if ( count( $list_page_id ) ) {
//            $categories = $catModel->findAll( array(
//                 'condition' => ' id IN ( ' . implode( ',', $list_page_id ) . ' ) ',
//            ) );
//            $this->tpl->assign( 'categories', $categories );
//        }

        $categories = $catModel->findAll();

        $this->tpl->assign( 'categories', $categories );
        return $this->tpl->fetch( 'gallery.categories' );
    }

    /**
     * Администрирование
     * @return mixed
     */
    function adminAction()
    {
        /**
         * @var model_gallery $model
         * @var model_galleryCategory $category
         */

        $this->request->setTitle( t( 'Images gallery' ) );

        $model = $this->getModel( 'Gallery' );

        $category = $this->getModel( 'GalleryCategory' );

        if( $switchimg = $this->request->get( 'switchimg', Request::INT ) ) {

            $obj    = $model->find( $switchimg );
            $switch_result = $model->hideSwitch( $obj->getId() );
            $obj->save();

            if( $switch_result !== false ) {
                $switch_icon = '';
                if( $switch_result == 1 ) {
                    $switch_icon = icon( 'lightbulb_off', 'Вкл' );
                } elseif( $switch_result == 2 ) {
                    $switch_icon = icon( 'lightbulb', 'Выкл' );
                }
                return array(
                    'id'    => $switchimg,
                    'img'   => $switch_icon,
                    'errno' => 0,
                );
            } else {
                return array(
                    'errno' => 1,
                    'error' =>  t( 'Switch error' ),
                );
            }
        }

        if( $this->request->get( 'positions' ) ) {
            return $model->reposition();
        }

        if( $editimage = $this->request->get( 'editimage', Request::INT ) ) {
            $this->setAjax();
            $editname    = $this->request->get( 'name' );
            $image       = $model->find( $editimage );
            $image->name = $editname;
            $image->save();
            return "$editimage => $editname";
        }

        $cat_list = $category->findAll();

        $this->tpl->categories = $cat_list;
        $this->request->setContent( $this->tpl->fetch( 'gallery.admin_category' ) );
        return 1;
    }

    /**
     * Удаление картинки
     * @return void
     */
    function deleteImageAction()
    {
        $model = $this->getModel( 'Gallery' );

        $img_id = $this->request->get( 'id', Request::INT );

        if( $img_id ) {
            if( $model->delete( $img_id ) ) {
                $this->request->setResponse( 'id', $img_id );
                $this->request->setResponseError( 0 );
            } else {
                $this->request->setResponseError( 1, t( 'Can not delete' ) );
            }
        }
    }

    /**
     * Редактирование категории
     * @return
     */
    public function editcatAction()
    {
        /**
         * @var Model_GalleryCategory $model
         * @var Data_Object_GalleryCategory $obj
         */
        $model = $this->getModel( 'GalleryCategory' );
        $form  = $model->getForm();

        if( $form->getPost() ) {
            if( $form->validate() ) {
                $obj    = $model->createObject( $form->getData() );
                $obj_id = $obj->getId();
                $model->save( $obj );

                if( $obj && ! $obj_id ) {
                    reload( 'admin/gallery' );
                }
                $this->request->addFeedback( t( 'Data save successfully' ) );
                return;
            }
            else {
                print $form->getFeedbackString();
                return;
            }
        }

        if( $edit = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT ) ) {
            try {
                $obj = $model->find( $edit );
            } catch( Exception $e ) {
                print $e->getMessage();
            }

            $form->setData( $obj->getAttributes() );
            if( get_class( $obj ) !== 'Data_Object_GalleryCategory' ) {
                $form->alias = $obj->getAlias();
            }
        }
        $this->tpl->form = $form;
        $this->request->setContent( $this->tpl->fetch( 'system:gallery.admin_category_edit' ) );
    }

    /**
     * Удалить категорию
     * @return mixed
     */
    public function delcatAction()
    {
        $model = $this->getModel( 'GalleryCategory' );
        //        $id = $this->request->get('delcat', FILTER_SANITIZE_NUMBER_INT);
        $id = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        if( $id ) {
            $model->remove( $id );
        }
        redirect( 'admin/gallery' );
    }

    /**
     * Просмотр категории
     * @return mixed
     */
    public function viewcatAction()
    {
        /** @var model_galleryCategory $category */
        $category = $this->getModel( 'GalleryCategory' );

        $cat_id = $this->request->get( 'id', Request::INT );

        $cat = $category->find( $cat_id );

        /** @var model_Gallery $model */
        $model = $this->getModel( 'Gallery' );


        if( isset( $_FILES[ 'image' ] ) ) {
            $this->upload( $cat );
        }

        $images = $model->findAll( array(
            'cond'  => 'category_id = :cat_id',
            'params'=> array( ':cat_id'=> $cat_id ),
            'order' => 'pos',
        ) );

        $this->tpl->assign(array(
            'images'   => $images,
            'category' => $cat->getAttributes(),
        ));

        return $this->tpl->fetch( 'system:gallery.admin_images' );
    }


    /**
     * Редактирование картинки
     * @var model_gallery $model
     * @return mixed
     */
    function editimgAction()
    {
        $model = $this->getModel( 'Gallery' );
        $this->request->setAjax( 1, Request::TYPE_ANY );
        $form = $this->getForm( 'gallery_image' );

        /** @var Data_Object_Gallery $obj */
        if( $form->getPost() ) {
            if( $form->validate() ) {
                $obj  = $model->find( $this->request->get( 'id' ) );
                $data = $form->getData();
                $obj->setAttributes( $data );
                $obj->save();
                $this->request->addFeedback( $obj->getAlias() );
                $this->request->addFeedback( t( 'Data save successfully' ) );
            }
            else {
                $this->request->addFeedback( $form->getFeedbackString() );
            }
        } else {
            $editimg = $this->request->get( 'id' );
            if( ! isset( $obj ) ) {
                $obj = $model->find( $editimg );
            }
            $atr            = $obj->getAttributes();
            $atr[ 'alias' ] = $obj->getAlias();
            $form->setData( $atr );
            return $form->html( false );
        }
    }

    /**
     * @return mixed
     */
    public function realiasAction()
    {
        $model = $this->getModel( 'Gallery' );
        $start = microtime( 1 );
        try {
            $images = $model->findAll();
            print '<ol>';
            /** @var Data_Object_GalleryCategory $cat */
            /** @var Data_Object_Gallery $img */
            foreach( $images as $img ) {
                try {
                    $img->save();
                } catch( Exception $e ) {
                    print $e->getMessage();
                }
                print "<li><b>{$img->name}</b> {$img->getAlias()}</li>";
            }
            print '</ol>';
        } catch( Exception $e ) {
            return $e->getMessage();
        }
        $this->request->setContent( round( microtime( 1 ) - $start, 3 ) . ' s.' );
    }

    /**
     * Загрузка файлов
     * @param Data_Object_GalleryCategory $cat
     * @return
     */
    protected function upload( Data_Object_GalleryCategory $cat )
    {
        /** @var Model_Gallery $model */
        $model         = $this->getModel();
        $max_file_size = $this->config->get( 'gallery.max_file_size' );
        $upload_ok     = 0;
        $thumb_prefix  = $this->config->get( 'gallery.thumb_prefix' );
        $middle_prefix = $this->config->get( 'gallery.middle_prefix' );

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
                        /** @var $image Data_Object_Gallery */
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
                            $tmb          =
                                $dest . DIRECTORY_SEPARATOR . '_' . $g_id . '_' . $thumb_prefix . $images[ 'name' ][ $i ];
                            $mdl          =
                                $dest . DIRECTORY_SEPARATOR . '_' . $g_id . '_' . $middle_prefix . $images[ 'name' ][ $i ];
                            $image->image = str_replace( DIRECTORY_SEPARATOR, '/', $img );
                            if( move_uploaded_file( $src, ROOT . $img ) ) {
                                // обработка
                                $thumb_h  = $cat->thumb_height;
                                $thumb_w  = $cat->thumb_width;
                                $middle_h = $cat->middle_height;
                                $middle_w = $cat->middle_width;
                                $t_method = $cat->thumb_method;
                                $m_method = $cat->middle_method;
                                try {
                                    $img_full = new Sfcms_Image( ROOT . $img );
                                    $img_mid  = $img_full->createThumb( $middle_w, $middle_h, $m_method, $cat->color );
                                    if( $img_mid ) {
                                        $img_mid->saveToFile( ROOT . $mdl );
                                        $image->middle = str_replace( DIRECTORY_SEPARATOR, '/', $mdl );
                                        unset( $img_mid );
                                    }
                                    $img_thmb = $img_full->createThumb( $thumb_w, $thumb_h, $t_method, $cat->color );
                                    if( $img_thmb ) {
                                        $img_thmb->saveToFile( ROOT . $tmb );
                                        $image->thumb = str_replace( DIRECTORY_SEPARATOR, '/', $tmb );
                                        unset( $img_thmb );
                                    }
                                } catch( Exception $e ) {
                                    $this->request->addFeedback( $e->getMessage() );
                                }
                            }
                            $model->save( $image );
                            if ( 0 == $image->pos ) {
                                $cat->thumb = $image->thumb;
                                $cat->save();
                            }
                        } else {
                            $this->request->addFeedback( "Превышен максимальный предел {$images['size'][$i]} из $max_file_size" );
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
        }
        else {
            $this->request->addFeedback( t( 'Image not loaded' ) );
        }
        return;
    }
}

