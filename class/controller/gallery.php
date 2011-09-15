<?php
/**
 * Контроллер галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link    http://siteforever.ru
 * @link    http://ermin.ru
 */
class Controller_Gallery extends Controller
{

    function init()
    {
        $default = array(
            'dir' => DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'gallery',
            'thumb_prefix'  => 'thumb_',
            'middle_prefix' => 'middle_',
        );
        if ( defined('MAX_FILE_SIZE') ) {
            $default['max_file_size']   = MAX_FILE_SIZE;
        } else {
            $default['max_file_size']   = 2*1024*1024;
        }
        $this->config->setDefault('gallery', $default);
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
     * @return void
     */
    public function indexAction()
    {
        $this->request->setTemplate('inner');
        /**
         * @var model_gallery $model
         */
        $model  = $this->getModel('Gallery');
        /**
         * @var model_galleryCategory $model_category
         */
        $model_category = $this->getModel('GalleryCategory');

        if ( $img = $this->request->get('img', Request::INT) )
        {
            $image  = $model->find( $img );

            if ( null !== $image ) {

                $crit   = array(
                    'cond'  => 'category_id = ? AND pos > ?',
                    'params'=> array( $image->category_id, $image->pos ),
                    'order' => 'pos ASC',
                    'limit' => '1',
                );

                $next   = $model->find( $crit );

                $crit['cond']   = 'category_id = ? AND pos < ?';
                $crit['order']  = 'pos DESC';

                $pred   = $model->find( $crit );

                $category   = $model_category->find( $image->category_id );

                $this->tpl->image   = $image;
                $this->tpl->next    = $next;
                $this->tpl->pred    = $pred;
                $this->tpl->category= $category;

                $bc     = $this->tpl->getBreadcrumbs();
                $bc->clearPieces();
                $bc->addPiece('index', 'Главная');
                $bc->addPiece($category->getAlias(), $category->name);
                $bc->addPiece('', $image->name);

                $title  =  $image->meta_title ? $image->meta_title : $category->name . ' - ' . $image->name;
//                $h1       = $image->meta_h1 ? $image->meta_h1 : $category->name . ' - ' . $image->name;
                $h1       = $image->meta_h1 ? $image->meta_h1 : $title;
                $this->tpl->meta_h1= $h1;

                $description    = $image->meta_description ? $image->meta_description : null;
                $keywords       = $image->meta_keywords ? $image->meta_keywords : null;
                if( $description ){
                    $this->request->set('tpldata.page.description',str_random_replace($h1, $description));
                }
                if( $keywords ) {
                    $this->request->set('tpldata.page.keywords',str_random_replace($h1, $keywords));
                }

//                $this->request->setTitle( $category->name . ' &rarr; ' . $image->name );
                $this->request->setTitle( $title );

                $this->request->setContent(
                    $this->tpl->fetch('gallery.image')
                );

                return;
            } else {
                $this->request->setContent( t('Image not found') );
                return;
            }
        }

        $cat_id = $this->request->get( 'cat', FILTER_SANITIZE_NUMBER_INT, $this->page['link'] );
        if ( $this->request->get('id', FILTER_SANITIZE_NUMBER_INT) ) {
            $cat_id = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);
        }

        if ( ! $cat_id ) {
            $this->request->addFeedback('Не указан идентификатор категории');
            return;
        }

        $category = $model_category->find( $cat_id );

        if ( $category ) {

            $crit   = array(
                'cond'      => 'hidden = 0 AND category_id = ?',
                'params'    => array( $category->getId() ),
            );

            $count  = $model->count( $crit['cond'], $crit['params'] );

            $paging = $this->paging( $count, $category->perpage, $this->page['alias'].'/cat='.$category['id'] );

            $crit['limit']  = $paging['limit'];
            $crit['order']  = 'pos';

            $rows   = $model->findAll( $crit );


//              print_r($rows);
            $this->tpl->category= $category->getAttributes();
            $this->tpl->rows    = $rows;
            $this->tpl->page    = $this->page;
            $this->tpl->paging  = $paging;

            $title  =   $category->meta_title ? $category->meta_title : $category->name;
//            $h1       = $category->meta_h1 ? $category->meta_h1 : $category->name;
            $h1       = $category->meta_h1 ? $category->meta_h1 : $title;

            $description    = $category->meta_description ? $category->meta_description : null;
            $keywords       = $category->meta_keywords ? $category->meta_keywords : null;
            $this->tpl->meta_h1= $h1;
            if( $description ){
                $this->request->set('tpldata.page.description',str_random_replace($h1, $description));
            }
            if( $keywords ) {
                $this->request->set('tpldata.page.keywords',str_random_replace($h1, $keywords));
            }

            $this->request->setTitle( $title );
            $this->request->setContent( $this->tpl->fetch('gallery.category') );

        } else {
            $this->request->addFeedback('Категория не определена');
        }
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

        $this->request->setTitle(t('Images gallery'));

        $model    = $this->getModel('Gallery');

        $category = $this->getModel('GalleryCategory');

//        if ( $this->request->get( 'viewcat', Request::INT ) ) {
//            return $this->viewCat( $category );
//        }

//        if ( $this->request->get('newcat') || $this->request->get('editcat') ) {
//            return $this->editCat( $category );
//        }

//        if ( $this->request->get('delcat') ) {
//            $this->deleteCat( $category );
//        }

//        if ( $this->request->get('editimg') ) {
//            return $this->editImage( $model );
//        }

        if ( $switchimg = $this->request->get('switchimg', Request::INT) ) {

            $switch_result = $model->hideSwitch( $switchimg );
            
            if ( $switch_result !== false ) {
                if ( $switch_result == 1 ) {
                    $switch_icon = icon('lightbulb_off', 'Вкл');
                }
                elseif ( $switch_result == 2 ) {
                    $switch_icon = icon('lightbulb', 'Выкл');
                }
                //$this->request->
                $this->request->setResponseError(0);
                $this->request->setResponse('id', $switchimg);
                $this->request->setResponse('img', $switch_icon);
            }
            else {
                $this->request->setResponseError(1, t('Switch error'));
            }
            return 1;
        }

        if ( $this->request->get('positions') ) {
            print $model->reposition();
            return 1;
        }

        if ( $editimage = $this->request->get('editimage', Request::INT) ) {
            $this->setAjax();
            $editname   = $this->request->get('name');
            $image  = $model->find($editimage);
            $image->name    = $editname;
            print "$editimage => $editname";
            return 1;
        }

        $cat_list = $category->findAll();

        $this->tpl->categories  = $cat_list;
        $this->request->setContent( $this->tpl->fetch('gallery.admin_category') );
        return 1;
    }

    /**
     * Удаление картинки
     * @return void
     */
    function deleteImageAction()
    {
        $model  = $this->getModel('Gallery');

        $img_id = $this->request->get('id', Request::INT);

        if ( $img_id ) {
            if ( $model->delete( $img_id ) ) {
                $this->request->setResponse('id', $img_id);
                $this->request->setResponseError(0);
            } else {
                $this->request->setResponseError(1, t('Can not delete'));
            }
        }
    }

    /**
     * Редактирование категории
     * @param model_GalleryCategory $model
     * @return
     */
//    function editCat( Model_GalleryCategory $model )
    function editcatAction( )
    {   $model = $this->getModel('GalleryCategory');
        $form = $model->getForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
//                var_dump($obj->getAttributes());
                //$obj->markNew();

                $model->save( $obj );

                if (  $obj && ! $obj->getId() ) {
                    reload('admin/gallery');
                }
                $this->request->addFeedback(t('Data save successfully'));
                return;
            }
            else {
                print $form->getFeedbackString();
                return;
            }
        }

//        if ( $edit = $this->request->get('editcat', FILTER_SANITIZE_NUMBER_INT) ) {
        if ( $edit = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT) ) {
            try {
                $obj    = $model->find( $edit );
            } catch ( Exception $e ) {
                print $e->getMessage();
            }

            $form->setData( $obj->getAttributes() );
            if(get_class($obj)!=='Data_Object_GalleryCategory'){
               $form->alias    = $obj->getAlias();
            }
        }
//    printVar($form);
        $this->tpl->form    = $form;
        $this->request->setContent( $this->tpl->fetch('system:gallery.admin_category_edit') );
    }

    /**
     * Удалить категорию
     * @param Model_GalleryCategory $model
     * @return void
     */
//    function deleteCat( Model_GalleryCategory $model )
    function delcatAction( )
    {   $model = $this->getModel('GalleryCategory');
//        $id = $this->request->get('delcat', FILTER_SANITIZE_NUMBER_INT);
        $id = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);
        if ( $id ) {
            $model->remove( $id );
        }
        redirect('admin/gallery');
    }

    /**
     * Просмотр категории
     * @return void
     */
//    function viewCat()
    function viewcatAction()
    {
        /**
         * @var model_galleryCategory $category
         */
        $category   = $this->getModel('GalleryCategory');
        
//        $cat_id = $this->request->get('viewcat', Request::INT);
        $cat_id = $this->request->get('id', Request::INT);

        $cat    = $category->find( $cat_id );
        
        /**
         * @var model_Gallery $model
         */
        $model  = $this->getModel('Gallery');
        

        if ( isset( $_FILES['image'] ) ) {
            $this->upload( $cat );
        }

        $images = $model->findAll(array(
            'cond'  => 'category_id = :cat_id',
            'params'=> array(':cat_id'=>$cat_id),
            'order' => 'pos',
        ));

        $this->tpl->images  = $images;
        $this->tpl->category= $cat->getAttributes();

        $this->request->setContent( $this->tpl->fetch('system:gallery.admin_images') );
    }


    /**
     * Редактирование картинки
     * @var model_gallery $model
     * @return void
     */
//    function editImage( model_gallery $model )
    function editimgAction( )
    {
        $model = $this->getModel('Gallery');

        $this->request->setAjax(1, Request::TYPE_ANY);

        $form   = $this->getForm('gallery_image');

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
//                $obj    = $model->find( $this->request->get('editimg') );
                $obj    = $model->find( $this->request->get('id') );
                $data = $form->getData();
                $obj->setAttributes( $data );
                $obj->save();
                $this->request->addFeedback( $obj->getAlias() );
                $this->request->addFeedback( t('Data save successfully') );
            }
            else {
                $this->request->addFeedback( $form->getFeedbackString() );
            }
            //return;
        } else {
//            $editimg    = $this->request->get('editimg');
            $editimg    = $this->request->get('id');
            if ( ! isset( $obj ) ) {
                $obj = $model->find( $editimg );
            }
            $atr = $obj->getAttributes();
            $atr['alias'] = $obj->getAlias();
            $form->setData( $atr );

            $this->request->setContent( $form->html(false) );
        }

//        $this->request->setContent( '' );
    }

    /**
     * @return void
     */
    public function realiasAction()
    {
        $model  = $this->getModel('Gallery');

        $start  = microtime(1);

//        $model->transaction();
        try {
            $images = $model->findAll();

            print '<ol>';
            /**
             * @var Data_Object_GalleryCategory $cat
             */
            foreach ( $images as $img ) {
                try {
                    $img->save();
                } catch ( Exception $e ) {
                    print $e->getMessage();
                }
                print "<li><b>{$img->name}</b> {$img->getAlias()}</li>";
            }
            print '</ol>';
//            $model->commit();
        } catch ( Exception $e ) {
//            $model->rollBack();
            print   $e->getMessage();
        }
        $this->request->setContent( round( microtime(1) - $start, 3 ).' s.' );
    }

    /**
     * Загрузка файлов
     * @param Data_Object_GalleryCategory $cat
     * @return
     */
    protected function upload( Data_Object_GalleryCategory $cat )
    {
        /**
         * @var Model_Gallery $model
         */
        $model  = $this->getModel('Gallery');

        $max_file_size = $this->config->get('gallery.max_file_size');

        $upload_ok = 0;

        $thumb_prefix   = $this->config->get('gallery.thumb_prefix');
        $middle_prefix  = $this->config->get('gallery.middle_prefix');

        if ( isset( $_FILES['image'] ) && is_array($_FILES['image']) )
        {
            $images = $_FILES['image'];

            $names  = array();

            if ( $this->request->get('name') ) {
                $names  = $this->request->get('name');
            }

            $pos    = $model->getNextPosition($cat->getId());
            $pos    = $pos ? $pos : 0;

            foreach ( $images['error'] as $i => $err )
            {
                if ( $err == UPLOAD_ERR_OK )
                {
                    $image  = $model->createObject(array(
                        'pos'   => $pos,
                        'main'  => '0',
                        'hidden'=> '0',
                    ));

                    $pos++;

                    if ( $images['size'][$i] <= $max_file_size &&
                            in_array( $images['type'][$i], array('image/jpeg', 'image/gif', 'image/png') )
                    ) {
                        $upload_ok = 1;

                        $dest = $this->config->get('gallery.dir').DIRECTORY_SEPARATOR.substr( '0000'.$cat->getId(), -4, 4 );
                        if ( ! is_dir( ROOT.$dest ) ) {
                            mkdir( ROOT.$dest, 0777, true );
                        }
                        $src  = $images['tmp_name'][$i];

                        $image->category_id = $cat->getId();
                        if ( isset( $names[ $i ] ) ) {
                            $image->name    = $names[ $i ];
                        }

                        $model->save( $image );
                        $g_id = $image->getId();

                        $img = $dest.DIRECTORY_SEPARATOR.$g_id.'_'.$images['name'][$i];
                        $tmb = $dest.DIRECTORY_SEPARATOR.'_'.$g_id.'_'.$thumb_prefix.$images['name'][$i];
                        $mdl = $dest.DIRECTORY_SEPARATOR.'_'.$g_id.'_'.$middle_prefix.$images['name'][$i];

                        $image->image   = str_replace( DIRECTORY_SEPARATOR, '/', $img );

                        //$model->set('image', str_replace( DIRECTORY_SEPARATOR, '/', $img ) );

                        if ( move_uploaded_file( $src, ROOT.$img ) )
                        {
                            // обработка
                            $thumb_h    = $cat->thumb_height;
                            $thumb_w    = $cat->thumb_width;
                            $middle_h   = $cat->middle_height;
                            $middle_w   = $cat->middle_width;
                            $t_method   = $cat->thumb_method;
                            $m_method   = $cat->middle_method;

                            try {
                                $img_full   = new Image(ROOT.$img);

                                $img_mid    = $img_full->createThumb($middle_w, $middle_h, $m_method, $cat->color);
                                if ( $img_mid ) {
                                    $img_mid->saveToFile( ROOT.$mdl );
                                    $image->middle  = str_replace( DIRECTORY_SEPARATOR, '/', $mdl );
                                    unset( $img_mid );
                                }

                                $img_thmb   = $img_full->createThumb($thumb_w, $thumb_h, $t_method, $cat->color);
                                if ( $img_thmb ) {
                                    $img_thmb->saveToFile( ROOT.$tmb );
                                    $image->thumb   = str_replace( DIRECTORY_SEPARATOR, '/', $tmb);
                                    unset( $img_thmb );
                                }
                            } catch ( Exception $e ) {
                                $this->request->addFeedback($e->getMessage());
                            }
                        }
                        $model->save( $image );
                    } else {
                        $this->request->addFeedback("Превышен максимальный предел {$images['size'][$i]} из $max_file_size");
                    }
                } else {
                    switch ( $err ) {
                        case UPLOAD_ERR_FORM_SIZE:
                            $this->request->addFeedback('form size error');
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $this->request->addFeedback('extension error');
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $this->request->addFeedback('partial error');
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $this->request->addFeedback('no file');
                            break;
                        default:
                            $this->request->addFeedback('unknown error');
                    }
                }
            }
        }

        if ( $upload_ok ) {
            $this->request->addFeedback(t('Images are loaded'));
        }
        else {
            $this->request->addFeedback(t('Image not loaded'));
        }
        return;
        //redirect('admin/catalog', array('edit'=>$upload));
    }
}

