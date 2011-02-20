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
            'dir' => DS.'files'.DS.'gallery',
            'thumb_prefix'  => 'thumb_',
            'middle_prefix'  => 'middle_',
        );
        if ( defined('MAX_FILE_SIZE') ) {
            $default['max_file_size']   = MAX_FILE_SIZE;
        } else {
            $default['max_file_size']   = 2*1024*1024;
        }
        $this->config->setDefault('gallery', $default);
    }

    function access()
    {
        return array(
            'system'    => array(
                'admin', 'edit', 'delete', 'deleteImage',
            ),
        );
    }

    /**
     * Действие по-умолчанию
     * @return void
     */
    function indexAction()
    {
        /**
         * @var model_gallery $model
         */
        $model  = $this->getModel('Gallery');
        /**
         * @var model_galleryCategory $model_category
         */
        $model_category = $this->getModel('GalleryCategory');

        $category = $model_category->find($this->page['link']);

        if ( $category ) {

            $crit   = array(
                'cond'      => 'hidden = 0 AND category_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            );

            $count  = $model->count($crit['cond'], $crit['params']);

            $paging = $this->paging($count, 10, $this->page['alias'].'/cat='.$category['id']);

            $crit['limit']  = $paging['limit'];
            $crit['order']  = 'pos';

            $rows   = $model->findAll( $crit );

            $this->tpl->category= $category->getAttributes();
            $this->tpl->rows    = $rows;
            $this->tpl->page    = $this->page;
            $this->tpl->paging  = $paging;
            $this->request->setContent( $this->tpl->fetch('gallery.index') );            

        } else {
            $this->request->addFeedback('Категория не определена');
        }

    }

    /**
     * Администрирование
     * @return void
     */
    function adminAction()
    {
        /**
         * @var model_gallery $model
         * @var model_galleryCategory $category
         */

        $this->request->setTitle(t('Images gallery'));

        $model    = $this->getModel('Gallery');
        //die(__FILE__.':'.__LINE__);
        $category = $this->getModel('GalleryCategory');

        if ( $this->request->get('viewcat', Request::INT) ) {
            return $this->viewCat( $category );
        }

        if ( $this->request->get('newcat') || $this->request->get('editcat') ) {
            return $this->editCat( $category );
        }

        if ( $this->request->get('delcat') ) {
            $this->deleteCat( $category );
        }

        if ( $this->request->get('editimg') ) {
            return $this->editImage($model);
        }

        if ( $switchimg = $this->request->get('switchimg', Request::INT) ) {

            $switch_result = $model->hideSwitch( $switchimg );
            
            if ( $switch_result !== false ) {
                if( $switch_result == 1 ) {
                    $switch_icon = icon('lightbulb_off', 'Вкл');
                }elseif( $switch_result == 2 ) {
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
            return;
        }

        if ( $positions = $this->request->get('positions') ) {
            print $model->reposition();
            return;
        }

        if ( $editimage = $this->request->get('editimage', Request::INT) ) {
            $this->setAjax();
            $editname   = $this->request->get('name');
            $image  = $model->find($editimage);
            $image->name    = $editname;
            print "$editimage => $editname";
            return;
        }

        $cat_list = $category->findAll();

        $this->tpl->categories  = $cat_list;
        $this->request->setContent( $this->tpl->fetch('gallery.admin_category') );
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
                $this->request->setResponseError(1, 'Can not delete');
            }
        }
    }

    /**
     * Редактирование категории
     * @param model_galleryCategory $model
     * @return
     */
    function editCat( Model_galleryCategory $model )
    {
        $form = $model->getForm();
        //die(__FILE__.':'.__LINE__);

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                $obj->markNew();
                if (  $obj && ! $obj->getId() ) {
                    reload('admin/gallery');
                }
                $this->request->addFeedback('Данные успешно сохранены');
                return;
            }
            else {
                print $form->getFeedbackString();
                return;
            }
        }

        if ( $edit = $this->request->get('editcat', FILTER_SANITIZE_NUMBER_INT) ) {
            $obj    = $model->find( $edit );
            $form->setData( $obj->getAttributes() );
        }

        $this->tpl->form    = $form;
        $this->request->setContent( $this->tpl->fetch('system:gallery.admin_category_edit') );
    }

    /**
     * Удалить категорию
     * @param model_galleryCategory $model
     * @return void
     */
    function deleteCat( model_galleryCategory $model )
    {
        $id = $this->request->get('delcat', FILTER_SANITIZE_NUMBER_INT);
        if ( $id ) {
            $model->remove( $id );
        }
        redirect('admin/gallery');
    }

    /**
     * Просмотр категории
     * @return void
     */
    function viewCat()
    {
        /**
         * @var model_galleryCategory $category
         */
        $category   = $this->getModel('GalleryCategory');
        
        $cat_id = $this->request->get('viewcat', Request::INT);

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
    function editImage( model_gallery $model )
    {
        $form   = $this->getForm('gallery_image');

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj    = $model->find( $this->request->get('editimg') );
                $obj->setAttributes( $form->getData() );
                $this->request->addFeedback(t('Data save successfully'));
            }
            else {
                $this->request->addFeedback($form->getFeedbackString());
            }
            return;
        }

        $editimg = $this->request->get('editimg');
        $obj    = $model->find( $editimg );

        $form->setData( $obj->getAttributes() );
        $this->request->setContent( $form->html(false) );
    }


    /**
     * Загрузка файлов
     * @param Data_Object_GalleryCategory $cat
     * @return
     */
    protected function upload( Data_Object_GalleryCategory $cat )
    {
        /**
         * @var Model_GalleryCategory $category
         */
        //$category   = $this->getModel('GalleryCategory');
        /**
         * @var Model_Gallery $model
         */
        $model  = $this->getModel('Gallery');

        $max_file_size = $this->config->get('gallery.max_file_size');

        //printVar($_FILES);

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

            //printVar( $images );
            //printVar( $names );


            $pos = $model->getNextPosition($cat->getId());

            //print $pos;

            //printVar($images);
            //return;

            foreach ( $images['error'] as $i => $err )
            {
                if ( $err == UPLOAD_ERR_OK )
                {
                    $image  = $model->createObject(array(
                        'pos'   => $pos,
                        'main'  => '0',
                        'hidden'=> '0',
                    ));

                    if ( $images['size'][$i] <= $max_file_size &&
                            in_array( $images['type'][$i], array('image/jpeg', 'image/gif', 'image/png') )
                    ) {
                        $upload_ok = 1;

                        $dest = $this->config->get('gallery.dir').DS.substr( '0000'.$cat->getId(), -4, 4 );
                        if ( ! is_dir( ROOT.$dest ) ) {
                            mkdir( ROOT.$dest, 0777, true );
                        }
                        $src  = $images['tmp_name'][$i];

                        $image->pos = $pos++;
                        $image->category_id = $cat->getId();
                        if ( isset( $names[ $i ] ) ) {
                            $image->name    = $names[ $i ];
                        }
                        
                        $model->save( $image );
                        $g_id = $image->getId();

                        $img = $dest.DS.$g_id.'_'.$images['name'][$i];
                        $tmb = $dest.DS.'_'.$g_id.'_'.$thumb_prefix.$images['name'][$i];
                        $mdl = $dest.DS.'_'.$g_id.'_'.$middle_prefix.$images['name'][$i];

                        $image->image   = str_replace( DS, '/', $img );

                        //$model->set('image', str_replace( DS, '/', $img ) );

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
                                if ( createThumb( ROOT.$img, ROOT.$mdl, $middle_w, $middle_h, $m_method) ) {
                                    $image->middle  = str_replace( DS, '/', $mdl );
                                };
                                if ( createThumb( ROOT.$img, ROOT.$tmb, $thumb_w, $thumb_h, $t_method) ) {
                                    $image->thumb   = str_replace( DS, '/', $tmb );
                                };
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
                //printVar($_FILES);
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
}

