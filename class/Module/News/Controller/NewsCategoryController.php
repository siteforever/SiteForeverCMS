<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 05.04.16
 * Time: 1:03
 */

namespace Module\News\Controller;


class NewsCategoryController
{

    /**
     * Править категорию для админки
     * @param int $id
     * @return mixed
     */
    public function catEditAction($id)
    {
        $this->request->setTitle($this->t('news','News category'));
        /** @var $newsModel NewsModel */
        $newsModel      = $this->getModel( 'News' );
        /** @var $categoryModel CategoryModel */
        $categoryModel  = $this->getModel( 'NewsCategory' );

        $form   = $categoryModel->getForm();

        if ( $form->handleRequest($this->request) ) {
            if ( $form->validate() ) {
                $data   = $form->getData();
                if ( $form->id ) {
                    $obj = $categoryModel->find( $form->id );
                    $obj->attributes = $data;
                } else {
                    $obj = $categoryModel->createObject( $data );
                    $obj->markNew();
                }
//                $this->reload('news/admin', array(), 2000);
                return array( 'error'=>0, 'msg'=>$this->t('Data save successfully') );
            } else {
                return array( 'error'=>1, 'msg'=>$form->getFeedbackString()) ;
            }
        }

        $news = null;
        if ($id) {
            $news   = $categoryModel->find($id);
            $form->setData( $news->getAttributes() );
        }

        if ($news) {
            return $this->render('news.catedit', array(
                'form'  => $form,
            ));
        }
        return $this->t('Unknown error');
    }

    /**
     * Удаление категории новостей и ее подновостей
     * @param int $id
     * @return mixed
     */
    public function catDeleteAction($id)
    {
        /**/
        $this->request->setTitle($this->t('news','News category'));
        $model      = $this->getModel('News');
        /**/
        $category   = $this->getModel('NewsCategory');

        try {
            /** @var $catObj Category */
            $catObj = $category->find($id);

            $news = $model->findAll( array(
                'cond'  => 'cat_id = :cat_id',
                'params'=> array( ':cat_id'=> $id ),
            ) );

            /** @var $obj News */
            foreach ( $news as $obj ) {
                $obj->deleted = 1;
            }

            $catObj->deleted = 1;
            $catObj->save();
        } catch ( Exception $e ) {
            return array('error'=>1,'msg'=>$e->getMessage());
        }

//        $this->reload('news/admin');
        return array('error'=>0,'msg'=> $this->t('news','News category was deleted') );
    }

}