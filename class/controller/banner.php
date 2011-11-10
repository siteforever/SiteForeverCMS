<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 16.09.11
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */
 
class Controller_Banner extends Controller
{

        function init()
    {
        $default = array(
            'dir' => DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'banner',
        );
        if ( defined('MAX_FILE_SIZE') ) {
            $default['max_file_size']   = MAX_FILE_SIZE;
        } else {
            $default['max_file_size']   = 2*1024*1024;
        }
        $this->config->setDefault('banner', $default);
    }

     public function indexAction()
    {

    }

     public function adminAction()
    {
        $this->request->setTitle("Управление баннерами");
        $category = $this->getModel('CategoryBanner');
        $cat_list = $category->findAll();
        $this->tpl->categories  = $cat_list;
        $this->request->setContent( $this->tpl->fetch('banner.category') );
        return 1;
    }

    /**
     * @param $url integer
     * @return boolean
     */
    public function redirectBannerAction()
    {
        $model = $this->getModel('Banner');
        $id = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT, null);
        $id_nt = $this->request->get('id');
          if(($id_nt!==null && $id === null) || (!is_numeric($id_nt) && $id_nt!==null )
            ) redirect('/error');
        $obj = $model->find($id);
        $obj['count_click']=$obj['count_click']+1;
        $model->save($obj);
        redirect($obj['url']);
        return true;
    }

    function editcatAction()
    {
        $model = $this->getModel('CategoryBanner');
        $form = $model->getForm();
        $this->request->setAjax(1, Request::TYPE_ANY);
        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                $model->save( $obj );
                $this->request->addFeedback(t('Data save successfully'));
            }
            else {
                print $form->getFeedbackString();
            }
            return;
        }
        if ( $edit = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT) ) {
            try {
                $obj    = $model->find( $edit );
            } catch ( Exception $e ) {
                print $e->getMessage();
            }
            $form->setData( $obj->getAttributes() );
        }
        $this->tpl->form    = $form;
        $this->request->setContent( $this->tpl->fetch('system:banner.editcat') );
        return 1;
    }

    function delcatAction()
    {
        $model = $this->getModel('CategoryBanner');
        $id = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);
        if ( $id ) {
            $model->remove( $id );
        }
        redirect('banner/admin');
    }

    function delAction()
    {
        $model = $this->getModel('Banner');
        $id = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);
        $cat            = $model->find($id);
        if ( $id ) {
            if ( $model->delete( $id ) ) {
                $this->request->setResponse('id', $id);
                $this->request->setResponseError(0);
            } else {
                $this->request->setResponseError(1, t('Can not delete'));
            }
            redirect('banner/cat/id/'.$cat['cat_id']);
        }
    }

    function catAction()
    {
        $model = $this->getModel('Banner');
        $category = $this->getModel('CategoryBanner');
        if ( $id = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT) ) {
            $this->request->setTitle('Управление баннерами');
            $count  = $model->count('`cat_id`='.$id);
            $paging = $this->paging( $count, 20, $this->router->createServiceLink('banner','cat', array('id'=>$id)) );
            $crit   = array();
            $crit['where']  = "`cat_id` = '$id'";
            $crit['limit']  = $paging->limit;
            $crit['order']  = 'name';
            $banners   = $model->findAll( $crit );
            $cat            = $category->find($id);
            if ( isset( $_FILES['image'] ) ) {
                $this->upload( $cat );
                redirect('banner/cat/id/'.$cat['id']);
            }
            $this->tpl->cat      = $cat;
            $this->tpl->banners  = $banners;
            $this->tpl->paging   = $paging;
            $this->request->setContent( $this->tpl->fetch('banner.banners') );
            return 1;
        } else {
            redirect('banner/admin');
        }
    }

    function editAction()
    {
        $model = $this->getModel('Banner');
        $form = $model->getForm();
        $this->request->setAjax(1, Request::TYPE_ANY);
        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                $model->save( $obj );
                $this->request->addFeedback(t('Data save successfully'));
            }
            else {
                print $form->getFeedbackString();
            }
            return;
        }
        if ( $edit = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT) ) {
            try {
                $obj    = $model->find( $edit );
            } catch ( Exception $e ) {
                print $e->getMessage();
            }
            $form->setData( $obj->getAttributes() );
            $category = $this->getModel('CategoryBanner');
            $cat = $category->find($obj['cat_id']);
            $this->tpl->cat         = $cat;
            $this->tpl->ban_name    = $obj['name'];
        } else {
            redirect('banner/admin');
        }
        $this->tpl->form        = $form;
        $this->request->setContent( $this->tpl->fetch('system:banner.editcat') );
        return 1;
    }

    /**
     * Уровень доступа к действиям
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
                'admin','redirectbanner', 'editcat','delcat','edit','del','cat'
            ),
        );
    }


    protected function upload( Data_Object_CategoryBanner $cat )
    {
//        /**
//         * @var Model_Banner $model
//         */
        $model  = $this->getModel('Banner');
        $max_file_size = 1000000;
        $upload_ok = 0;
        if ( isset( $_FILES['image'] ) && is_array($_FILES['image']) )
        {
            $images = $_FILES['image'];
            $names  = array();
            if ( $this->request->get('name') ) {
                $names  = $this->request->get('name');
            }
            foreach ( $images['error'] as $i => $err )
            {
                if ( $err == UPLOAD_ERR_OK )
                {
                    $image  = $model->createObject();

                    if ( $images['size'][$i] <= $max_file_size &&
                            in_array( $images['type'][$i], array('image/jpeg', 'image/gif', 'image/png') )
                    ) {
                        $upload_ok = 1;
                        $dest = DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'banner'.DIRECTORY_SEPARATOR.substr( '0000'.$cat->getId(), -4, 4 );
                        if ( ! is_dir( ROOT.$dest ) ) {
                            mkdir( ROOT.$dest, 0777, true );
                        }
                        $src  = $images['tmp_name'][$i];
                        $image->cat_id = $cat->getId();
                        if ( isset( $names[ $i ] ) ) {
                            $image->name    = $names[ $i ];
                        }
                        $model->save( $image );
                        $g_id = $image->getId();
                        $img = $dest.DIRECTORY_SEPARATOR.$g_id.'_'.$images['name'][$i];
                        $image->image   = str_replace( DIRECTORY_SEPARATOR, '/', $img );
                        if ( move_uploaded_file( $src, ROOT.$img ) )
                        {
                            $image->path  = str_replace( DIRECTORY_SEPARATOR, '/', $img );
                            $image->target = '_self';
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
    }

}
