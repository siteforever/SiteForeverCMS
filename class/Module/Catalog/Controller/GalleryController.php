<?php
/**
 * @author Nikolay Ermin
 * @link   http://siteforever.ru
 * @link   http://ermin.ru
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms_Controller;
use Request;
use Model_CatalogGallery;
use Data_Object_CatalogGallery;

class GalleryController extends Sfcms_Controller
{
    public function access()
    {
        return array(
            'system'    => array(
                'admin', 'delete', 'markdefault', 'upload', 'panel',
            ),
        );
    }

    public function defaults()
    {
        $ds = DIRECTORY_SEPARATOR;
        return array(
            'catalog',
            array(
                'gallery_dir'            =>
                $ds . 'files' . $ds . 'catalog' . $ds . 'gallery',
                'gallery_max_file_size'  => 1000000,
                // 1 - добавление полей
                // 2 - обрезание лишнего
            )
        );
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
        $catalog_gallery = $this->getModel( 'CatalogGallery' );
        $id              = $this->request->get( 'id', Request::INT );

        $image = $catalog_gallery->find( $id );

        if ( null === $image ) {
            return array('error'=>1,'msg'=>'Image not found');
        }

        $catalog_gallery->remove( $id );
        $cat_id = $image->cat_id;

        //$catalog_gallery->delete( $del );
        //$gallery = $catalog_gallery->findGalleryByProduct($cat);
        if ( $cat_id ) {
            return array('error'=>0,'msg'=>$this->getPanel( $cat_id ) );
        }
        return array('error'=>1,'msg'=>'Category not defined');
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
//        $this->setAjax();
//        $this->request->setAjax( true, Request::TYPE_ANY );

        $max_file_size = $this->config->get( 'catalog.gallery_max_file_size' );

        $prodId   = $this->request->get( 'prod_id' );
        $formSent = $this->request->get( 'sent' );

        if ( ! $formSent ) {
            return array(
                'prod_id' => $prodId,
                'max_file_size' => $max_file_size,
            );
        }

        $trade = $this->getModel('Catalog')->find( $prodId );

        $images = $trade->Gallery;

        $createMain = true;  // Делать ли первую картинку главной
        if ( $images && $images->count() ) {
            $createMain = false;
        }

        /**
         * @var Model_CatalogGallery $catalogGallery
         */
        $catalogGallery = $this->getModel( 'CatalogGallery' );

        $uploadOk = 0;

        if ( isset( $_FILES[ 'image' ] ) && is_array( $_FILES[ 'image' ] ) ) {
            $images = $_FILES[ 'image' ];
            //printVar($images);
            foreach ( $images[ 'error' ] as $i => $err ) {
                switch ( $err ) {
                    case UPLOAD_ERR_OK:
                        /** @var $objImage Data_Object_CatalogGallery */
                        $objImage = $catalogGallery->createObject();

                        if ( $images[ 'size' ][ $i ] <= $max_file_size
                            && in_array( $images[ 'type' ][ $i ], array( 'image/jpeg', 'image/gif', 'image/png' ) )
                        ) {
                            $uploadOk = 1;

                            $dest = $this->config->get( 'catalog.gallery_dir' )
                                . '/' . substr( '0000' . $prodId, -4, 4 );

                            if ( !is_dir( ROOT . $dest ) ) {
                                if ( @mkdir( ROOT . $dest, 0777, true ) ) {
                                    $this->request->addFeedback( t('catalog','Created directory ') . ROOT . $dest );
                                }
                            }

                            $src = $images[ 'tmp_name' ][ $i ];

                            $objImage->cat_id = $prodId;
                            $catalogGallery->save( $objImage );
                            $g_id = $objImage->getId();

                            $img = $dest . '/' . $g_id . '_' . $images[ 'name' ][ $i ];

                            if ( move_uploaded_file( $src, ROOT . $img ) ) {
                                $objImage->image = str_replace( array('/','\\'), '/', $img );
                            }
                            if ( $createMain ) {
                                $objImage->main = 1;
                                $createMain = false;
                            }

                            $objImage->save();
                        }
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $this->request->addFeedback( "При загрузке изображения $i произошла ошибка" );
                }

            }
        }

        if ( $formSent ) {
            if ( $uploadOk ) {
                return t('Изображения загружены');
            }
            return t('Изображения не загружены');
        }
    }

}
