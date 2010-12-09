<?php
class controller_gallery extends Controller
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

    /**
     * Действие по-умолчанию
     * @return void
     */
    function indexAction()
    {
        /**
         * @var model_gallery $model
         */
        $model  = $this->getModel('gallery');
        /**
         * @var model_galleryCategory $model_category
         */
        $model_category = $this->getModel('gallerycategory');

        $category = $model_category->find($this->page['link']);

        if ( $category ) {

            $count  = $model->getCount(array('hidden=0','category_id='.$category['id']));

            $paging = $this->paging($count, 10, $this->page['alias'].'/cat='.$category['id']);

            $rows   = $model->findAll(
                    array('hidden=0','category_id='.$category['id']),
                    $paging['from'].','.$paging['perpage']
            );

            $this->tpl->category= $category;
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
        //die(__FILE__.':'.__LINE__);

        $this->request->setTitle(t('Images gallery'));

        $category = $this->getModel('galleryCategory');
        $model    = $this->getModel('gallery');

        if ( $this->request->get('viewcat',FILTER_SANITIZE_NUMBER_INT) ) {
            return $this->viewCat( $category );
        }

        if ( $this->request->get('newcat') || $this->request->get('editcat') ) {
            return $this->editCat( $category );
        }

        if ( $this->request->get('delcat') ) {
            return $this->deleteCat( $category );
        }

        if ( $delimg = $this->request->get('delimg') ) {
            if ( $model->delete( $delimg ) ) {
                print json_encode(array('id'=>$delimg,'error'=>'0'));
            } else {
                print json_encode(array('error'=>'1'));
            }
            return;
        }

        if ( $editimg = $this->request->get('editimg') ) {
            return $this->editImage($model);
        }

        if ( $switchimg = $this->request->get('switchimg', FILTER_SANITIZE_NUMBER_INT) ) {
            $switch_result = $model->hideSwitch($switchimg);
            if ( $switch_result !== false ) {
                if( $switch_result == 1 ) {
                    $switch_icon = icon('lightbulb_off', 'Вкл');
                }elseif( $switch_result == 2 ) {
                    $switch_icon = icon('lightbulb', 'Выкл');
                }
                print json_encode(array('error'=>'0', 'id'=>$switchimg, 'img'=>$switch_icon));
            }
            else {
                print json_encode(array('error'=>'1'));
            }
            return;
        }

        if ( $positions = $this->request->get('positions') ) {
            print $model->reposition();
            return;
        }

        if ( $editimage = $this->request->get('editimage', FILTER_SANITIZE_NUMBER_INT) ) {
            $this->setAjax();
            $editname   = $this->request->get('name');
            $model->find($editimage);
            $model->set('name', $editname);
            $model->update();
            print "$editimage => $editname";
            return;
        }

        $cat_list = $category->findAll();

        $this->tpl->categories  = $cat_list;
        $this->request->setContent( $this->tpl->fetch('gallery.admin_category') );
    }

    /**
     * Редактирование категории
     * @param model_galleryCategory $model
     * @return
     */
    function editCat( model_galleryCategory $model )
    {
        $form = $model->getForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $model->setData( $form->getData() );
                if ( $model->get('id') ) {
                    $model->update();
                } else {
                    $model->insert();
                    reload('admin/gallery');
                }
                $this->request->addFeedback('Данные успешно сохранены');
                return;
            }
        }

        if ( $edit = $this->request->get('editcat', FILTER_SANITIZE_NUMBER_INT) ) {
            $model->find( $edit );
            $form->setData( $model->getData() );
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
            $model->delete( $id );
        }
        reload('admin/gallery');
    }

    /**
     * Просмотр категории
     * @param model_galleryCategory $category
     * @return void
     */
    function viewCat( model_galleryCategory $category )
    {
        $cat_id = $this->request->get('viewcat', FILTER_SANITIZE_NUMBER_INT);
        $category->find( $cat_id );

        /**
         * @var model_gallery $model
         */
        $model  = $this->getModel('gallery');



        if ( isset( $_FILES['image'] ) ) {
            $this->upload($category);
        }



        $images = $model->findAll(array('category_id = '.$cat_id));

        $this->tpl->images  = $images;
        $this->tpl->category= $category->getData();

        $this->request->setContent( $this->tpl->fetch('system:gallery.admin_images') );
    }


    /**
     * Редактирование картинки
     * @var model_gallery $model
     * @return void
     */
    function editImage( model_gallery $model )
    {
        $editimg = $this->request->get('editimg');
        $form   = $this->getForm('gallery_image');
        $model->find( $editimg );

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $model->setData($form->getData());
                $model->update();
                $this->request->addFeedback(t('Data save successfully'));
            }
            else {
                $this->request->addFeedback($form->getFeedbackString());
            }
            return;
        }

        $form->setData( $model->getData() );
        $this->request->setContent( $form->html(false) );
    }


    /**
     * Загрузка файлов
     * @param model_galleryCategory $category
     * @return
     */
    function upload( model_galleryCategory $category )
    {
        /**
         * @var model_gallery $model
         */
        $model  = $this->getModel('gallery');

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

            $pos = $model->getNextPosition($category->getId());

            //printVar($images);
            foreach ( $images['error'] as $i => $err )
            {
                $model->set('id', '');
                if ( $err == UPLOAD_ERR_OK )
                {
                    if ( $images['size'][$i] <= $max_file_size &&
                            in_array( $images['type'][$i], array('image/jpeg', 'image/gif', 'image/png') )
                    ) {
                        $upload_ok = 1;

                        $dest = $this->config->get('gallery.dir').DS.substr( '0000'.$category->getId(), -4, 4 );
                        if ( ! is_dir( ROOT.$dest ) ) {
                            mkdir( ROOT.$dest, 0777, true );
                        }
                        $src  = $images['tmp_name'][$i];

                        $model->set('pos', $pos++);
                        $model->set('category_id', $category->getId());
                        if ( isset( $names[ $i ] ) ) {
                            $model->set('name', $names[ $i ]);
                        }
                        
                        $model->insert();
                        $g_id = $model->getId();

                        $img = $dest.DS.$g_id.'_'.$images['name'][$i];
                        $tmb = $dest.DS.'_'.$g_id.'_'.$thumb_prefix.$images['name'][$i];
                        $mdl = $dest.DS.'_'.$g_id.'_'.$middle_prefix.$images['name'][$i];

                        $model->set('image', str_replace( DS, '/', $img ) );

                        if ( move_uploaded_file( $src, ROOT.$img ) )
                        {
                            // обработка
                            $thumb_h    = $category->get('thumb_height');
                            $thumb_w    = $category->get('thumb_width');
                            $middle_h   = $category->get('middle_height');
                            $middle_w   = $category->get('middle_width');
                            $t_method   = $category->get('thumb_method');
                            $m_method   = $category->get('middle_method');

                            try {
                                if ( createThumb( ROOT.$img, ROOT.$mdl, $middle_w, $middle_h, $m_method) ) {
                                    $model->set('middle',
                                        str_replace( DS, '/', $mdl )
                                    );
                                };
                                if ( createThumb( ROOT.$img, ROOT.$tmb, $thumb_w, $thumb_h, $t_method) ) {
                                    $model->set('thumb',
                                        str_replace( DS, '/', $tmb )
                                    );
                                };
                            } catch ( Exception $e ) {
                                $this->request->addFeedback($e->getMessage());
                            }
                        }
                        $model->update();
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

