<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Gallery\Controller;

use Sfcms\Controller;
use Module\Gallery\Form\ImageForm;
use Sfcms;
use Exception;
use Sfcms\Form\Form;
use Module\Gallery\Object\Gallery;
use Module\Gallery\Object\Category;
use Module\Gallery\Model\GalleryModel;
use Module\Gallery\Model\CategoryModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminController extends Controller
{
    /**
     * Уровень доступа к действиям
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN    => array(
                'admin', 'edit', 'list', 'delete', 'delcat', 'editcat', 'switchimg',
            ),
        );
    }

    /**
     * Администрирование
     * @return mixed
     */
    public function adminAction()
    {
        /**
         * @var GalleryModel $model
         * @var CategoryModel $category
         */

        $this->request->setTitle($this->t('Images gallery'));
        $model    = $this->getModel('Gallery');
        $category = $this->getModel('GalleryCategory');

        if ($this->request->request->has('editimage')) {
            $image       = $model->find($this->request->request->getInt('editimage'));
            $image->name = $this->request->request->get('name');

            return 'ok';
        }

        if ($this->request->request->has('positions')) {
            return $model->reposition($this->request);
        }

        $cat_list = $category->findAll('deleted != 1');

        return $this->render('gallery.admin.admin', array(
                'categories' => $cat_list,
            ));
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function switchimgAction($id)
    {
        /** @var $model GalleryModel */
        $model = $this->getModel('Gallery');

        if ($id) {
            $obj    = $model->find( $id );
            $switch_result = $model->hideSwitch( $obj->getId() );
            $obj->save();

            if( $switch_result !== false ) {
                $switch_icon = '';
                if( $switch_result == 1 ) {
                    $switch_icon = Sfcms::html()->icon( 'lightbulb_off', 'Вкл' );
                } elseif( $switch_result == 2 ) {
                    $switch_icon = Sfcms::html()->icon( 'lightbulb', 'Выкл' );
                }
                return $this->renderJson(array(
                        'id'    => $id,
                        'img'   => $switch_icon,
                        'error' => 0,
                        'msg'   => '',
                    ));
            } else {
                return $this->renderJson(array(
                        'error' => 1,
                        'msg' =>  $this->t( 'Switch error' ),
                    ));
            }
        }
    }

    /**
     * Удаление картинки
     * @param int $id
     *
     * @return array|mixed
     */
    public function deleteAction($id)
    {
        $model = $this->getModel('Gallery');
        if ($id) {
            $image = $model->find($id);
            $image->deleted = 1;
            if ($image->save()) {
                return array(
                    'error' => 0,
                    'msg' => $this->t('Image was deleted'),
                    'id' => $id,
                );
            }

            return array('error' => 1, 'msg' => $this->t('Can not delete'));
        }

        return $this->t('Image not was deleted');
    }

    /**
     * Редактирование категории
     * @param int $id
     * @return mixed
     */
    public function editcatAction($id)
    {
        /**
         * @var CategoryModel $model
         * @var Category $obj
         */
        $model = $this->getModel( 'GalleryCategory' );
        $form  = $model->getForm();

        if( $form->handleRequest($this->request) ) {
            if( $form->validate() ) {
                $obj    = $form->id ? $model->find($form->id) : $model->createObject();
                $obj->attributes = $form->getData();
                $model->save( $obj );
                return array('error'=>0,'msg'=>$this->t( 'Data save successfully' ),'name'=>$obj->name,'id'=>$obj->id);
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }

        if ($id) {
            try {
                $obj = $model->find($id);
                $this->request->setTitle( $obj->name );
            } catch( Exception $e ) {
                return $e->getMessage();
            }

            $form->setData( $obj->getAttributes() );
            if (get_class($obj) !== 'Module\\Gallery\\Object\\Category') {
                $form->alias = $obj->getAlias();
            }
        }
        return $this->render('gallery.admin.editcat', array('form' => $form->createView()));
    }

    /**
     * Удалить категорию
     * @param int $id
     * @return mixed
     */
    public function delcatAction($id)
    {
        /** @var CategoryModel */
        $model = $this->getModel('GalleryCategory');
        $cat   = $model->find($id);
        if ($cat) {
            $cat->deleted = 1;
        }
        return $this->redirect('gallery/admin');
    }

    /**
     * Просмотр категории
     * @param int $id
     *
     * @throws \RuntimeException
     * @return array
     */
    public function listAction($id)
    {
        if (!$id) {
            throw new \RuntimeException('Parameter "id" is not defined');
        }
        /** @var CategoryModel $category */
        $category = $this->getModel('GalleryCategory');

        $cat = $category->find($id);

        /** @var GalleryModel $model */
        $model = $this->getModel('Gallery');

        if ($this->request->files->has('image')) {
            $this->upload($cat);
            return $this->redirect('gallery/list', ['id'=>$id]);
        }

        $images = $model->findAll(array(
                'cond'  => 'category_id = :cat_id AND deleted = 0',
                'params'=> array( ':cat_id'=> $id ),
                'order' => 'pos',
            ));

        $this->request->setTitle($cat->name);
        return $this->render('gallery/admin/list', array(
            'images'   => $images,
            'category' => $cat->getAttributes(),
        ));
    }


    /**
     * Редактирование картинки
     * @var GalleryModel $model
     * @return mixed
     */
    public function editAction()
    {
        $model = $this->getModel('Gallery');
        /** @var $form Form */
        $form = new ImageForm();

        /** @var Gallery $obj */
        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $obj = $form['id'] ? $model->find($form['id']) : $model->createObject();
                $obj->attributes = $form->getData();
                $obj->save();

                return array(
                    'error' => 0,
                    'msg'   => $this->t('Data save successfully'),
                    'name'  => $obj->name,
                    'id'    => $obj->id,
                );
            } else {
                return array('error' => 1, 'msg' => $form->getFeedbackString());
            }
        }
        $editimg = $this->request->get('id');
        if (!isset($obj)) {
            $obj = $model->find($editimg);
        }
        $atr = $obj->getAttributes();
        $form->setData($atr);
        $obj->markClean();

        return $this->render('gallery.admin.edit', array('form'=>$form));
    }

    /**
     * Загрузка файлов
     * @param Category $cat
     * @return void
     */
    protected function upload( Category $cat )
    {
        /** @var GalleryModel $model */
        $model         = $this->getModel('Gallery');
        $uploadOk     = 0;
        $config = $this->container->getParameter('gallery');
        if ($this->request->files->has('image')) {
            $images = $this->request->files->get('image');
            $names  = array();

            if ($this->request->request->has('name')) {
                $names = $this->request->request->get('name');
            }

            $pos = $model->getNextPosition( $cat->getId() );
            $pos = $pos ? $pos : 0;

            foreach ($images as $i => $file) {
                /** @var $file UploadedFile */
                if (!$file) {
                    continue;
                }
                if ($file->isValid()) {
                    if (!in_array($file->getClientMimeType(), $config['mime'])) {
                        $this->request->addFeedback($this->t('Mime type not access in').' '.$file->getClientOriginalName(), 'error');
                        continue;
                    }
                    /** @var $image Gallery */
                    $image = $model->createObject();
                    $image->pos = $pos++;
                    $image->main = 0;
                    $image->hidden = 0;
                    $image->name = isset($names[$i]) ? $names[$i] : $names[0];
                    $image->category_id = $cat->getId();
                    $image->setUploadedFile($file);

                    $uploadOk = 1;
                } else {
                    $this->request->addFeedback($file->getErrorMessage(), 'error');
                }
            }
        }
        if ($uploadOk) {
            $this->request->addFeedback($this->t('Images are loaded'), 'success');
        } else {
            $this->request->addFeedback($this->t('Image not loaded'), 'error');
        }
        return;
    }
}
