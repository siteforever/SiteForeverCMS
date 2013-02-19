<?php
/**
 * Контроллер новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Controller;

use Sfcms_Controller;
use Sfcms\Request;
use Sfcms\Form\Form;
use Module\News\Model\NewsModel;
use Module\News\Model\CategoryModel;
use Module\News\Object\News;
use Module\News\Object\Category;
use Sfcms_Http_Exception;
use Exception;

class NewsController extends Sfcms_Controller
{
    /**
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array('admin','list','edit','catedit','catdelete','delete'),
        );
    }

    /**
     * @return mixed
     */
    public function indexAction()
    {
        $model = $this->getModel('News');
        if ( $this->request->get('doc') || $this->request->get('alias') ) {
            return $this->getNews($model);
        } else {
            return $this->getNewsList($model);
        }
    }

    /**
     * Отображать новость на сайте
     * @param NewsModel $model
     * @return mixed
     */
    public function getNews( NewsModel $model )
    {
        $id = intval( $this->request->get('doc' ) );
        $alias = $this->request->get( 'alias' );
        /** @var $news News */
        if ( $id ) {
            $news = $model->find( $id );
        } elseif ( $alias ) {
            $news = $model->findByAlias( $alias );
        }

        if ( ! $news ) {
            throw new Sfcms_Http_Exception(t('news','Article not found'), 404);
        }

        // работаем над хлебными крошками
        $bc = $this->getTpl()->getBreadcrumbs();
        $bc->addPiece( null, $news->title );

        $this->tpl->assign('news', $news);

        $this->request->setTitle( $news->title );

        if ( ! $this->user->hasPermission( $news['protected'] ) ) {
            throw new Sfcms_Http_Exception( t('Access denied'), 403 );
        }
        return $this->tpl->fetch('news.item');
    }

    /**
     * Отображать список новостей на сайте
     * @param NewsModel $model
     * @return mixed
     */
    public function getNewsList( NewsModel $model )
    {
        /** @var Category $category */
        $category = $model->category->find( $this->page['link'] );

        if ( ! $category ) {
            return $this->tpl->fetch('news.catempty');
        }

        $cond   = '`deleted` = 0 AND `hidden` = 0 AND `cat_id` = ?';
        $params = array($category->getId());

        $count  = $model->count($cond, $params);

        $paging     = $this->paging( $count, $category->per_page, $this->page->alias );

//        $list   = $model->findAllWithLinks(array(
        $list   = $model->findAll(array(
            'cond'     => $cond,
            'params'   => $params,
            'limit'    => $paging['limit'],
            'order'    => '`date` DESC, `id` DESC',
        ));

        $this->tpl->assign(array(
            'paging'    => $paging,
            'list'      => $list,
            'cat'       => $category,
        ));

        switch ( $category['type_list'] ) {
            case 2:
                $template   = 'news.items_list';
                break;
            default:
                $template   = 'news.items_blog';
        }

        return $this->tpl->fetch( $template );
    }

    /**
     * Управление новостями
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->setTitle(t('news','News'));
        $this->app()->addScript('/misc/admin/news.js');

        /** @var NewsModel $model */
        $model      = $this->getModel('News');
        $category   = $model->category;

        $list   = $category->findAll(array('cond'=>'deleted = 0'));
        return array(
            'list'  => $list,
        );
    }

    /**
     * Список новостей для админки
     * @return mixed
     */
    public function listAction()
    {
        /**/
        $this->request->setTitle(t('news','News'));
        $this->app()->addScript('/misc/admin/news.js');

        $model      = $this->getModel('News');
        /**/
        $catId =  $this->request->get('id', Request::INT);

        $count  = $model->count('cat_id = :cat_id', array(':cat_id'=>$catId));
        $paging = $this->paging( $count, 20, $this->router->createServiceLink('news','list',array('id'=>$catId)));

        $list = $model->findAll(array(
            'cond'      => 'cat_id = :cat_id AND deleted = 0',
            'params'    => array(':cat_id'=>$catId),
            'limit'     => $paging->limit,
            'order'     => '`date` DESC, `id` DESC',
        ));

        $cat    = $this->getModel('NewsCategory')->find( $catId );

        return array(
            'paging'    => $paging,
            'list'      => $list,
            'cat'       => $cat,
        );
    }

    /**
     * Редактрование новости для админки
     * @return mixed
     */
    public function editAction( )
    {
        $this->request->setTitle(t('news','News edit'));
        /** @var $model NewsModel */
        $model      = $this->getModel('News');
        /** @var $form Form */
        $form   = $model->getForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj    = $form->id ? $model->find($form->id) : $model->createObject();
                $obj->attributes = $form->getData();
                return array('error'=>0, 'msg'=>t('Data save successfully'));
            }
            return array('error'=>1, 'msg'=>$this->request->getFeedbackString());
        }

        $edit   = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);

        if ( $edit ) {
            $news   = $model->find( $edit );
            $form->setData( $news->getAttributes() );
        } else {
            $news   = $model->createObject();
        }

        $cat    = null;
        if ( isset( $news['cat_id'] ) && $news['cat_id'] ) {
            $cat    = $model->category->find( $news['cat_id'] );
        }
        if ( null === $cat && $this->request->get('cat', Request::INT) ) {
            $cat    = $model->category->find( $this->request->get('cat', Request::INT) );
        }

        return array(
            'form'  => $form,
            'cat'   => $cat,
        );
    }

    /**
     * Править категорию для админки
     * @return mixed
     */
    public function cateditAction( )
    {
        $this->request->setTitle(t('news','News category'));
        /** @var $newsModel NewsModel */
        $newsModel      = $this->getModel( 'News' );
        /** @var $categoryModel CategoryModel */
        $categoryModel  = $this->getModel( 'NewsCategory' );

        $form   = $categoryModel->getForm();

        if ( $form->getPost() ) {
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
                return array( 'error'=>0, 'msg'=>t('Data save successfully') );
            } else {
                return array( 'error'=>1, 'msg'=>$form->getFeedbackString()) ;
            }
        }

        $edit   = $this->request->get('id', Request::INT);

        if ( $edit ) {
            $news   = $categoryModel->find( $edit );
            $form->setData( $news->getAttributes() );
        }

        if ( $edit !== false ) {
            $this->tpl->assign(array(
                'form'  => $form,
            ));
            return $this->tpl->fetch('news.catedit');
        }
        return t('Unknown error');
    }

    /**
     * Удаление категории новостей и ее подновостей
     * @return mixed
     */
    public function catdeleteAction( )
    {
        /**/
        $this->request->setTitle(t('news','News category'));
        $model      = $this->getModel('News');
        /**/
        $category   = $this->getModel('NewsCategory');
        $catId     = $this->request->get('id', Request::INT);

        try {
            /** @var $catObj Category */
            $catObj = $category->find( $catId );

            $news = $model->findAll( array(
                'cond'  => 'cat_id = :cat_id',
                'params'=> array( ':cat_id'=> $catId ),
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
        return array('error'=>0,'msg'=> t('news','News category was deleted') );
    }

    /**
     * @return mixed
     */
    public function deleteAction()
    {
        /**/
        $this->request->setTitle(t('News'));
        $model      = $this->getModel('News');
        /**/
        $newsId    = $this->request->get('id', Request::INT);
        /** @var $obj News */
        $obj    = $model->find( $newsId );
//        $catId = $obj->cat_id;
        $obj->deleted = 1;
//        $this->reload('news/list', array('id'=>$catId));
        return array('error'=>0,'msg'=>t('news','News was delete'));
    }
}