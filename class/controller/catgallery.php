<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class controller_catGallery extends Controller
{
    /**
     * @todo Надо добавить аяксовости в галерею
     */
    function init()
    {
        $default = array(
            'gallery_dir' => DS.'files'.DS.'catalog'.DS.'gallery',
            'gallery_max_file_size' => 1000000,
            'gallery_thumb_prefix'  => 'thumb_',
            'gallery_thumb_h'   => 100,
            'gallery_thumb_w'   => 100,
            'gallery_thumb_method' => 1,
            'gallery_middle_prefix'  => 'middle_',
            'gallery_middle_h'   => 200,
            'gallery_middle_w'   => 200,
            'gallery_middle_method' => 1,
            // 1 - добавление полей
            // 2 - обрезание лишнего
        );
        $this->config->setDefault('catalog', $default);
    }

    function indexAction()
    {
        $this->setAjax();
        $this->request->setAjax( true, Request::TYPE_ANY );

        $max_file_size = $this->config->get('catalog.gallery_max_file_size');

        //$catalog = Model::getModel('model_Catalog');
        $catalog_gallery = $this->getModel('CatGallery');

        $prod = $this->request->get('prod');

        if ( $prod ) {
            $this->tpl->prod            = $prod;
            $this->tpl->max_file_size   = $max_file_size;
            $this->tpl->display('system:catgallery.upload_form');
        }

        $upload = $this->request->get('upload');

        $thumb_prefix = $this->config->get('catalog.gallery_thumb_prefix');
        $middle_prefix = $this->config->get('catalog.gallery_middle_prefix');

        if ( $reload = $this->request->get( 'reload', FILTER_SANITIZE_NUMBER_INT ) ) {

            $gallery    = $catalog_gallery->findAll(array(
                'cond'      => 'cat_id = ?',
                'params'    => array( $reload ),
            ));
                //$gallery = $catalog_gallery->findGalleryByProduct($reload);
            $this->tpl->gallery = $gallery;
            $this->tpl->cat     = $reload;
            $this->request->setContent( $this->tpl->fetch('system:catgallery.admin_panel') );
            return;
        }

        if ( $upload )
        {
            $obj_image  = $catalog_gallery->createObject();

            //printVar($_FILES);

            $upload_ok = 0;

            if ( isset( $_FILES['image'] ) && is_array($_FILES['image']) )
            {
                $images = $_FILES['image'];
                //printVar($images);
                foreach ( $images['error'] as $i => $err )
                {
                    if ( $err == UPLOAD_ERR_OK )
                    {
                        if ( $images['size'][$i] <= $max_file_size &&
                                in_array( $images['type'][$i], array('image/jpeg', 'image/gif', 'image/png') )
                        ) {
                            $upload_ok = 1;

                            $dest = $this->config->get('catalog.gallery_dir').DS.substr( '0000'.$upload, -4, 4 );

                            if ( ! is_dir( ROOT.$dest ) ) {
                                mkdir( ROOT.$dest, 0777, true );
                            }

                            $src  = $images['tmp_name'][$i];

                            $obj_image->cat_id  = $upload;
                            $catalog_gallery->save( $obj_image );
                            $g_id   = $obj_image->getId();
                            //$catalog_gallery->set('cat_id', $upload);
                            //$catalog_gallery->insert();
                            //$g_id = $catalog_gallery->getId();

                            $img = $dest.DS.$g_id.'_'.$images['name'][$i];
                            $tmb = $dest.DS.'_'.$g_id.'_'.$thumb_prefix.$images['name'][$i];
                            $mdl = $dest.DS.'_'.$g_id.'_'.$middle_prefix.$images['name'][$i];

                            if ( move_uploaded_file( $src, ROOT.$img ) )
                            {
                                // обработка
                                $obj_image->image   = str_replace( DS, '/', $img );

                                $thumb_h    = $this->config->get('catalog.gallery_thumb_h');
                                $thumb_w    = $this->config->get('catalog.gallery_thumb_w');
                                $middle_h   = $this->config->get('catalog.gallery_middle_h');
                                $middle_w   = $this->config->get('catalog.gallery_middle_w');
                                $t_method   = $this->config->get('catalog.gallery_thumb_method');
                                $m_method   = $this->config->get('catalog.gallery_middle_method');

                                if ( createThumb( ROOT.$img, ROOT.$mdl, $middle_w, $middle_h, $m_method) ) {
                                    $obj_image->middle  = str_replace( DS, '/', $mdl );
                                };
                                if ( createThumb( ROOT.$img, ROOT.$tmb, $thumb_w, $thumb_h, $t_method) ) {
                                    $obj_image->thumb   = str_replace( DS, '/', $tmb );
                                };
                            }
                        }
                    }
                }
            }

            if ( $upload_ok ) {
                $this->request->addFeedback('Изображения загружены');
            }
            else {
                $this->request->addFeedback('Изображения не загружены');
            }
            return;
            //redirect('admin/catalog', array('edit'=>$upload));
        }

        $cat = $this->request->get('cat', FILTER_SANITIZE_NUMBER_INT);

        $del = $this->request->get('del', FILTER_SANITIZE_NUMBER_INT);

        $gallery    = $catalog_gallery->findAll(array(
            'cond'      => 'cat_id = ?',
            'params'    => array( $reload ),
        ));

        if ( $del ) {
            //$catalog_gallery->delete( $del );
            $catalog_gallery->remove( $del );
            //$gallery = $catalog_gallery->findGalleryByProduct($cat);
            $this->tpl->gallery = $gallery;
            $this->tpl->cat     = $cat;
            $this->request->setContent($this->tpl->fetch('system:catgallery.admin_panel'));
            return;
            //redirect('admin/catalog', array('edit'=>$cat));
        }

        $main = $this->request->get('main', FILTER_SANITIZE_NUMBER_INT);
        
        if ( $main ) {
            $catalog_gallery->setDefault( $main, $cat );
            //$gallery = $catalog_gallery->findGalleryByProduct($cat);
            $this->tpl->gallery = $gallery;
            $this->tpl->cat     = $cat;
            $this->request->setContent($this->tpl->fetch('system:catgallery.admin_panel'));
            return;
            //redirect('admin/catalog', array('edit'=>$cat));
        }
    }

    /**
     * Пересоздает миниатюрные изображения
     */
    function regenerateAction()
    {
        $time   = microtime(1);
        set_time_limit(0);
        
        $catalog_gallery = $this->getModel('CatGallery');
        
        $images = $catalog_gallery->findAll();
        
        //printVar($images);
            
        $thumb_h    = $this->config->get('catalog.gallery_thumb_h');
        $thumb_w    = $this->config->get('catalog.gallery_thumb_w');
        $middle_h   = $this->config->get('catalog.gallery_middle_h');
        $middle_w   = $this->config->get('catalog.gallery_middle_w');
        $t_method   = $this->config->get('catalog.gallery_thumb_method');
        $m_method   = $this->config->get('catalog.gallery_middle_method');

        foreach( $images as $img ) {
        
            $img_file = SF_PATH.$img['image'];
            $mid_file = SF_PATH.$img['middle'];
            $tmb_file = SF_PATH.$img['thumb'];

            if ( file_exists( SF_PATH.$img['image'] ) ) {
                if ( $img['middle'] && file_exists( SF_PATH.$img['middle'] ) ) {
                    createThumb( $img_file, $mid_file, $middle_w, $middle_h, $m_method );
                }
                if ( $img['thumb'] && file_exists( SF_PATH.$img['thumb'] ) ) {
                    createThumb( $img_file, $tmb_file, $thumb_w, $thumb_h, $t_method );
                }
            }
        }
        
        $this->request->addFeedback('Регенерация изображений закончена');
        $this->request->addFeedback('Затрачено: '.round(microtime(1) - $time, 4).' сек');
    }

}
