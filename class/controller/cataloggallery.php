<?php
/**
 * @author Nikolay Ermin
 * @link   http://siteforever.ru
 * @link   http://ermin.ru
 */
class Controller_CatalogGallery extends Sfcms_Controller
{
    public function init()
    {
        $default = array(
            'gallery_dir'            =>
            DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'gallery',
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

    public function indexAction()
    {
        $this->setAjax();
        $this->request->setAjax( true, Request::TYPE_ANY );

        if ( $id = $this->request->get( 'id', Request::INT ) ) {
            $this->tpl->cat = $id;
            return $this->getPanel( $id );
        }
        return t('Not found parametr ID');
    }

    /**
     * Удаление изображения
     * @return mixed
     */
    public function deleteAction()
    {
        $this->setAjax();
        $catalog_gallery = $this->getModel( 'CatalogGallery' );
        $id              = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );

        $image = $catalog_gallery->find( $id );

        if ( null !== $image ) {
            $catalog_gallery->remove( $id );
            $cat_id = $image->cat_id;
        } else {
            $this->request->setContent( 'Image not found' );
            return;
        }


        //$catalog_gallery->delete( $del );
        //$gallery = $catalog_gallery->findGalleryByProduct($cat);
        if ( $cat_id ) {
            $this->request->setContent( $this->getPanel( $cat_id ) );
        }
        //return $this->redirect('admin/catalog', array('edit'=>$cat));

    }

    /**
     * Пометить как картинке по умолчанию
     * @return mixed
     */
    public function markdefaultAction()
    {
        $this->setAjax();
        /** @var Model_CatalogGallery $catGalleryModel */
        $catGalleryModel = $this->getModel( 'CatalogGallery' );
        $id              = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        /** @var $image Data_Object_CatalogGallery */
        $image = $catGalleryModel->find( $id );

        if ( null !== $image ) {
            $cat_id = $image->cat_id;
            $catGalleryModel->setDefault( $id, $cat_id );
            if ( $cat_id ) {
                return $this->getPanel( $cat_id );
            } else {
                return t( 'Category not defined' );
            }
        }
        return t( 'Image not found' );
    }

    /**
     * Вернет HTML код для админ-панели картинок
     * @param $id
     * @return string
     */
    public function getPanel( $id )
    {
        /** @var Model_CatalogGallery $catalogGallery */
        $catalogGallery = $this->getModel();
        $images = $catalogGallery->findAll(
            array(
                 'cond'      => ' cat_id = ? ',
                 'params'    => array( $id ),
            )
        );
        $this->tpl->gallery = $images;
        $this->tpl->cat     = $id;
        return $this->tpl->fetch('system:cataloggallery.panel');
    }

    /**
     * Загрузка изображений
     */
    public function uploadAction()
    {
        $this->setAjax();
        $this->request->setAjax( true, Request::TYPE_ANY );

        $max_file_size = $this->config->get( 'catalog.gallery_max_file_size' );

        $prod_id   = $this->request->get( 'prod_id' );
        $form_sent = $this->request->get( 'sent' );

        if ( !$form_sent ) {
            return array(
                'prod_id' => $prod_id,
                'max_file_size' => $max_file_size,
            );
        }

        $thumb_prefix  = $this->config->get( 'catalog.gallery_thumb_prefix' );
        $middle_prefix = $this->config->get( 'catalog.gallery_middle_prefix' );

        /**
         * @var Model_CatalogGallery $catalog_gallery
         */
        $catalog_gallery = $this->getModel( 'CatalogGallery' );

        //        printVar($_FILES);

        $upload_ok = 0;

        if ( isset( $_FILES[ 'image' ] ) && is_array( $_FILES[ 'image' ] ) ) {
            $images = $_FILES[ 'image' ];
            //printVar($images);
            foreach ( $images[ 'error' ] as $i => $err ) {
                switch ( $err ) {
                    case UPLOAD_ERR_OK:

                        $obj_image = $catalog_gallery->createObject();

                        if ( $images[ 'size' ][ $i ] <= $max_file_size
                            && in_array( $images[ 'type' ][ $i ], array( 'image/jpeg', 'image/gif', 'image/png' ) )
                        ) {
                            $upload_ok = 1;

                            $dest = $this->config->get( 'catalog.gallery_dir' )
                                . DIRECTORY_SEPARATOR . substr( '0000' . $prod_id, -4, 4 );

                            if ( !is_dir( ROOT . $dest ) ) {
                                if ( @mkdir( ROOT . $dest, 0777, true ) ) {
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
                            $tmb = $dest . DIRECTORY_SEPARATOR . '_' . $g_id . '_' . $thumb_prefix
                                . $images[ 'name' ][ $i ];
                            $mdl = $dest . DIRECTORY_SEPARATOR . '_' . $g_id . '_' . $middle_prefix
                                . $images[ 'name' ][ $i ];

                            if ( move_uploaded_file( $src, ROOT . $img ) ) {
                                // обработка
                                $obj_image->image = str_replace( DIRECTORY_SEPARATOR, '/', $img );

                                $thumb_h  = $this->config->get( 'catalog.gallery_thumb_h' );
                                $thumb_w  = $this->config->get( 'catalog.gallery_thumb_w' );
                                $middle_h = $this->config->get( 'catalog.gallery_middle_h' );
                                $middle_w = $this->config->get( 'catalog.gallery_middle_w' );
                                $t_method = $this->config->get( 'catalog.gallery_thumb_method' );
                                $m_method = $this->config->get( 'catalog.gallery_middle_method' );

                                try {
                                    $img_full = new Sfcms_Image( ROOT . $img );
                                    $img_mid  = $img_full->createThumb( $middle_w, $middle_h, $m_method );
                                    if ( $img_mid ) {
                                        $img_mid->saveToFile( ROOT . $mdl );
                                        $obj_image->middle = str_replace( DIRECTORY_SEPARATOR, '/', $mdl );
                                        unset( $img_mid );
                                    }
                                    $img_thmb = $img_full->createThumb( $thumb_w, $thumb_h, $t_method );
                                    if ( $img_thmb ) {
                                        $img_thmb->saveToFile( ROOT . $tmb );
                                        $obj_image->thumb = str_replace( DIRECTORY_SEPARATOR, '/', $tmb );
                                        unset( $img_thmb );
                                    }
                                } catch ( Exception $e ) {
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

        if ( $form_sent ) {
            if ( $upload_ok ) {
                return t('Изображения загружены');
            }
            return t('Изображения не загружены');
        }
    }

    /**
     * Пересоздает миниатюрные изображения
     */
    public function regenerateAction()
    {
        $time = microtime( 1 );
        set_time_limit( 0 );

        $catalog_gallery = $this->getModel( 'CatalogGallery' );

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

            $image = new Sfcms_Image( $img_file );

            $middle = $image->createThumb( $middle_w, $middle_h, $m_method );
            $middle->saveToFile( $mid_file );

            $thumb = $image->createThumb( $thumb_w, $thumb_h, $t_method );
            $thumb->saveToFile( $tmb_file );
        }

        $this->request->addFeedback( 'Регенерация изображений закончена' );
        $this->request->addFeedback( 'Затрачено: ' . round( microtime( 1 ) - $time, 4 ) . ' сек' );
    }

    public function access()
    {
        return array(
            'system'    => array(
                'admin', 'delete', 'markdefault', 'upload', 'panel',
            ),
        );
    }
}
