<?php
/**
 * @author Nikolay Ermin
 * @link   http://siteforever.ru
 * @link   http://ermin.ru
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms;
use Sfcms\Controller;
use Sfcms\Request;
use Module\Catalog\Model\GalleryModel;
use Module\Catalog\Object\Catalog;
use Module\Catalog\Object\Gallery;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GalleryController extends Controller
{
    public function access()
    {
        return array(
            'system'    => array(
                'admin', 'delete', 'markdefault', 'upload', 'panel','watermark',
            ),
        );
    }

    /**
     * @param int $id
     *
     * @return mixed|string
     */
    public function indexAction( $id )
    {
        $this->request->setAjax( true, Request::TYPE_ANY );

        $positions = $this->request->get('positions');
        if ( $positions ) {
            $positions = array_flip( $positions );
            /** @var GalleryModel $catalogGallery */
            $catalogGallery = $this->getModel('CatalogGallery');
            $images = $catalogGallery->findAll('cat_id = ?', array( $id ),'pos');
            array_map(function(Gallery $img) use ($positions) {
                if ( isset( $positions[$img->id] ) ) {
                    $img->pos = $positions[$img->id];
                }
            }, iterator_to_array( $images ));
            return array('error'=>0);
        }

        if ( $id ) {
            $this->tpl->cat = $id;
            return $this->getPanel( $id );
        }
        return $this->t('Not found parametr ID');
    }

    /**
     * Удаление изображения
     * @param int $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        /** @var $catalog_gallery GalleryModel */
        $catalog_gallery = $this->getModel( 'CatalogGallery' );

        /** @var $image Gallery */
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
        $this->request->setAjax(true);
        /** @var GalleryModel $catGalleryModel */
        $catGalleryModel = $this->getModel('CatalogGallery');
        $id              = $this->request->get('id');
        /** @var $image Gallery */
        $image = $catGalleryModel->find($id);

        if ( null !== $image ) {
            $cat_id = $image->cat_id;
            $catGalleryModel->setDefault($id, $cat_id);
            if ( $cat_id ) {
                return $this->getPanel($cat_id);
            } else {
                return $this->t('Category not defined');
            }
        }
        return $this->t('Image not found');
    }

    /**
     * Вернет HTML код для админ-панели картинок
     * @param int $id
     * @return string
     */
    public function getPanel($id)
    {
        /** @var GalleryModel $catalogGallery */
        $catalogGallery = $this->getModel('CatalogGallery');
        $images = $catalogGallery->findAll('cat_id = ?', array($id), 'pos');

        $hasMain = array_reduce(iterator_to_array($images), function($result, Gallery $obj){
            return $result || (bool) $obj->main;
        }, false);

        if ( ! $hasMain && $images->count() ) {
            $images->rewind()->main = 1;
        }
        $this->tpl->gallery = $images;
        $this->tpl->cat     = $id;
        $this->tpl->request = $this->request;
        return $this->tpl->fetch('cataloggallery.panel');
    }

    /**
     * Загрузка изображений
     */
    public function uploadAction()
    {
        $this->request->setAjax(true, Request::TYPE_ANY);


        $prodId   = $this->request->get('prod_id');
        $formSent = $this->request->get('sent');

        if (!$formSent) {
            return array(
                'prod_id'       => $prodId,
            );
        }

        /** @var $trade Catalog */
        $trade = $this->getModel('Catalog')->find($prodId);

        $images = $trade->Gallery;
        $createMain = !($images && $images->count()); // Делать ли первую картинку главной

        /**
         * @var GalleryModel $galleryModel
         */
        $galleryModel = $this->getModel('CatalogGallery');
        $filesystem = new Filesystem();

        $dest = $this->config->get('catalog.gallery_dir') . '/'
            . substr(md5($prodId), 0, 2) . '/' . substr('000000' . $prodId, -6, 6) . '/';

        /** @var UploadedFile $file */
        foreach ($this->request->files->get('image') as $i => $file) {

            try {
                $trade->uploadImage($this->config->get('catalog.gallery_dir') , $file);
            } catch (\RuntimeException $e) {
                $this->request->addFeedback($e->getMessage());
                continue;
            }

//            if (!in_array($file->getClientMimeType(), array('image/jpeg', 'image/gif', 'image/png'))) {
//                $this->request->addFeedback(sprintf('Unsupported image type "%s"', $file->getClientMimeType()));
//                continue;
//            }
//
//            /** @var $objImage Gallery */
//            $objImage = $galleryModel->createObject();
//            $objImage->trade  = $trade;
//            $objImage->cat_id = $prodId;
//            $objImage->hidden = 0;
//            $objImage->pos    = 100;
//            $objImage->main   = 0;
//            $objImage->save();
//            $g_id = $objImage->getId();
//
//            // Для thumb храним нормальное изображение в хэше, а для image накладываем watermark
//            // Это чистое изображение, но имя зашифровано
//            $tmb = strtolower(substr(md5(microtime(1) . $trade->alias), 0, 6) . '.' . $file->getClientOriginalExtension());
//            // Имя не зашифровано, но с водяным знаком
//            $img = strtolower($g_id . '-' . $trade->alias . '.' . $file->getClientOriginalExtension());
//
//            try {
//                /** @var File $target */
//                $target = $file->move(ROOT . $dest, $tmb);
//            } catch (FileException $e) {
//                $this->request->addFeedback($e->getMessage());
//                $objImage->delete();
//                continue;
//            }
//
//            if ($createMain) {
//                $objImage->main = 1;
//                $createMain = false;
//            }
//            $objImage->thumb = $dest . $tmb;
//            $objImage->image = $dest . $img;
//            if (!Sfcms::watermark($target->getRealPath(), ROOT . $objImage->image)) {
//                $filesystem->rename($target->getRealPath(), ROOT . $objImage->image, true);
//                $objImage->thumb = $objImage->image;
//            }
//            $objImage->save();
        }

        return $this->getPanel($prodId);
    }

    /**
     * Testing watermark overlay
     * @return bool|resource
     */
    public function watermarkAction()
    {
//        return Sfcms::watermark( ROOT . '/files/catalog/gallery/000030/59-30-chemodan-edmins-g-30.jpg' );
        return Sfcms::watermark( ROOT . '/files/catalog/gallery/0007/15_129м-01\'.jpg' );
//        return Sfcms::watermark( ROOT . '/files/catalog/gallery/0007/14_129м-01.jpg' );
    }

}
