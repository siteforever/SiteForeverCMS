<?php
/**
 * @author Nikolay Ermin
 * @link   http://siteforever.ru
 * @link   http://ermin.ru
 */
class Controller_CatGallery extends Controller
{
    function init()
    {
        $default = array(
            'gallery_dir'            => DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'gallery',
            'gallery_max_file_size'  => 1000000,
            'gallery_thumb_prefix'   => 'thumb_',
            'gallery_thumb_h'        => 100,
            'gallery_thumb_w'        => 100,
            'gallery_thumb_method'   => 1,
            'gallery_middle_prefix'  => 'middle_',
            'gallery_middle_h'       => 200,
            'gallery_middle_w'       => 200,
            'gallery_middle_method'  => 1,
            // 1 - добавление полей
            // 2 - обрезание лишнего
        );
        $this->config->setDefault( 'catalog', $default );
    }

    function indexAction()
    {
        $this->setAjax();
        $this->request->setAjax( true, Request::TYPE_ANY );

        //$catalog = Model::getModel('model_Catalog');
        $catalog_gallery = $this->getModel( 'CatGallery' );

        if ($id = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT )) {
            $this->tpl->cat = $id;
            $this->request->setContent( $this->getAdminPanel( $id ) );
            return;
        }

        //        if ( $upload )
        //        {
        //            return;
        //            //redirect('admin/catalog', array('edit'=>$upload));
        //        }

        //        $cat = $this->request->get('cat', FILTER_SANITIZE_NUMBER_INT);

        //        $gallery    = $catalog_gallery->findAll(array(
        //            'cond'      => 'cat_id = ?',
        //            'params'    => array( $cat ),
        //        ));
        //
        //        $main = $this->request->get('main', FILTER_SANITIZE_NUMBER_INT);

        $this->request->setContent( 'Not found parametr ID' );
    }

    /**
     * Удаление изображения
     * @return mixed
     */
    function deleteAction()
    {
        $this->setAjax();
        $catalog_gallery = $this->getModel( 'CatGallery' );
        $id              = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );

        $image = $catalog_gallery->find( $id );

        if (null !== $image) {
            $catalog_gallery->remove( $id );
            $cat_id = $image->cat_id;
        }
        else {
            $this->request->setContent( 'Image not found' );
            return;
        }


        //$catalog_gallery->delete( $del );
        //$gallery = $catalog_gallery->findGalleryByProduct($cat);
        if ($cat_id) {
            $this->request->setContent( $this->getAdminPanel( $cat_id ) );
        }
        //redirect('admin/catalog', array('edit'=>$cat));

    }

    /**
     * Пометить как картинке по умолчанию
     * @return mixed
     */
    function markdefaultAction()
    {
        $this->setAjax();
        /**
         * @var Model_CatGallery $catalog_gallery
         */
        $catalog_gallery = $this->getModel( 'CatGallery' );
        $id              = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        $image           = $catalog_gallery->find( $id );

        if (null !== $image) {
            $cat_id = $image->cat_id;
            $catalog_gallery->setDefault( $id, $cat_id );
        }
        else {
            $this->request->setContent( 'Image not found' );
            return;
        }


        if ($cat_id) {
            $this->request->setContent( $this->getAdminPanel( $cat_id ) );
        }
        //redirect('admin/catalog', array('edit'=>$cat));
    }

    /**
     * Вернет HTML код для админ-панели картинок
     *
     * @param $cat_id
     *
     * @return string
     */
    public function getAdminPanel( $cat_id )
    {
        //        $this->setAjax();
        /**
         * @var Model_CatGallery $catalog_gallery
         */
        $catalog_gallery = $this->getModel( 'CatGallery' );

        $images = $catalog_gallery->findAll(
            array(
                'cond'      => ' cat_id = ? ',
                'params'    => array( $cat_id ),
            )
        );
        //        print __METHOD__;
        //$gallery = $catalog_gallery->findGalleryByProduct($cat);
        $this->tpl->gallery = $images;
        $this->tpl->cat     = $cat_id;
        return $this->tpl->fetch( 'system:catgallery.admin_panel' );
    }

    /**
     * Загрузка изображений
     */
    function uploadAction()
    {
        $this->setAjax();
        $this->request->setAjax( true, Request::TYPE_ANY );

        $max_file_size = $this->config->get( 'catalog.gallery_max_file_size' );

        $prod_id   = $this->request->get( 'prod_id' );
        $form_sent = $this->request->get( 'sent' );

        if (!$form_sent) {
            $this->tpl->prod_id       = $prod_id;
            $this->tpl->max_file_size = $max_file_size;
            $this->tpl->display( 'system:catgallery.upload_form' );
        }

        $thumb_prefix  = $this->config->get( 'catalog.gallery_thumb_prefix' );
        $middle_prefix = $this->config->get( 'catalog.gallery_middle_prefix' );

        /**
         * @var Model_CatGallery $catalog_gallery
         */
        $catalog_gallery = $this->getModel( 'CatGallery' );

        //        printVar($_FILES);

        $upload_ok = 0;

        if (isset( $_FILES[ 'image' ] ) && is_array( $_FILES[ 'image' ] )) {
            $images = $_FILES[ 'image' ];
            //printVar($images);
            foreach ( $images[ 'error' ] as $i => $err )
            {
                switch ( $err ) {
                    case UPLOAD_ERR_OK:

                        $obj_image = $catalog_gallery->createObject();

                        if ($images[ 'size' ][ $i ] <= $max_file_size
                            && in_array( $images[ 'type' ][ $i ], array( 'image/jpeg', 'image/gif', 'image/png' ) )
                        ) {
                            $upload_ok = 1;

                            $dest = $this->config->get( 'catalog.gallery_dir' )
                                . DIRECTORY_SEPARATOR . substr( '0000' . $prod_id, -4, 4 );

                            if (!is_dir( ROOT . $dest )) {
                                if (@mkdir( ROOT . $dest, 0777, true )) {
                                    $this->request->addFeedback( "Создан каталог " . ROOT . $dest );
                                }
                            }

                            $src = $images[ 'tmp_name' ][ $i ];

                            $obj_image->cat_id = $prod_id;
                            $catalog_gallery->save( $obj_image );
                            $g_id = $obj_image->getId();
                            //$catalog_gallery->set('cat_id', $upload);
                            //$catalog_gallery->insert();
                            //$g_id = $catalog_gallery->getId();

                            $img = $dest . DIRECTORY_SEPARATOR . $g_id . '_' . $images[ 'name' ][ $i ];
                            $tmb = $dest . DIRECTORY_SEPARATOR . '_' . $g_id . '_' . $thumb_prefix . $images[ 'name' ][ $i ];
                            $mdl = $dest . DIRECTORY_SEPARATOR . '_' . $g_id . '_' . $middle_prefix . $images[ 'name' ][ $i ];

                            if (move_uploaded_file( $src, ROOT . $img )) {
                                // обработка
                                $obj_image->image = str_replace( DIRECTORY_SEPARATOR, '/', $img );

                                $thumb_h  = $this->config->get( 'catalog.gallery_thumb_h' );
                                $thumb_w  = $this->config->get( 'catalog.gallery_thumb_w' );
                                $middle_h = $this->config->get( 'catalog.gallery_middle_h' );
                                $middle_w = $this->config->get( 'catalog.gallery_middle_w' );
                                $t_method = $this->config->get( 'catalog.gallery_thumb_method' );
                                $m_method = $this->config->get( 'catalog.gallery_middle_method' );

                                try {
                                    $img_full = new Image( ROOT . $img );
                                    $img_mid  = $img_full->createThumb( $middle_w, $middle_h, $m_method );
                                    if ($img_mid) {
                                        $img_mid->saveToFile( ROOT . $mdl );
                                        $obj_image->middle = str_replace( DIRECTORY_SEPARATOR, '/', $mdl );
                                        unset( $img_mid );
                                    }
                                    $img_thmb = $img_full->createThumb( $thumb_w, $thumb_h, $t_method );
                                    if ($img_thmb) {
                                        $img_thmb->saveToFile( ROOT . $tmb );
                                        $obj_image->thumb = str_replace( DIRECTORY_SEPARATOR, '/', $tmb );
                                        unset( $img_thmb );
                                    }
                                }
                                catch ( Exception $e ) {
                                    $this->request->addFeedback( $e->getMessage() );
                                }
                            }
                            $obj_image->save();
                        }
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $this->request->addFeedback( "При загрузке изображения $i произошла ошибка" );
                }

            }
        }

        if ($form_sent) {
            if ($upload_ok) {
                $this->request->setContent( 'Изображения загружены' );
            }
            else {
                $this->request->setContent( 'Изображения не загружены' );
            }
        }
    }

    /**
     * Пересоздает миниатюрные изображения
     */
    function regenerateAction()
    {
        $time = microtime( 1 );
        set_time_limit( 0 );

        $catalog_gallery = $this->getModel( 'CatGallery' );

        $images = $catalog_gallery->findAll();

        //printVar($images);

        $thumb_h  = $this->config->get( 'catalog.gallery_thumb_h' );
        $thumb_w  = $this->config->get( 'catalog.gallery_thumb_w' );
        $middle_h = $this->config->get( 'catalog.gallery_middle_h' );
        $middle_w = $this->config->get( 'catalog.gallery_middle_w' );
        $t_method = $this->config->get( 'catalog.gallery_thumb_method' );
        $m_method = $this->config->get( 'catalog.gallery_middle_method' );

        foreach ( $images as $img ) {

            $img_file = SF_PATH . $img[ 'image' ];
            $mid_file = SF_PATH . $img[ 'middle' ];
            $tmb_file = SF_PATH . $img[ 'thumb' ];

            $image = new \sfcms\Image( $img_file );

            $middle = $image->createThumb( $middle_w, $middle_h, $m_method );
            $middle->saveToFile( $mid_file );

            $thumb  = $image->createThumb( $thumb_w, $thumb_h, $t_method );
            $thumb->saveToFile( $tmb_file );
        }

        $this->request->addFeedback( 'Регенерация изображений закончена' );
        $this->request->addFeedback( 'Затрачено: ' . round( microtime( 1 ) - $time, 4 ) . ' сек' );
    }

    function access()
    {
        return array(
            'system'    => array(
                'admin', 'delete', 'markdefault', 'upload',
            ),
        );
    }
}
