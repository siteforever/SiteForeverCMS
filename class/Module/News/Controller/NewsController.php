<?php
/**
 * Контроллер новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Controller;

use Sfcms\Controller;
use Sfcms\Request;
use Sfcms\Form\Form;
use Module\News\Model\NewsModel;
use Module\News\Model\CategoryModel;
use Module\News\Object\News;
use Module\News\Object\Category;
use Sfcms_Http_Exception;
use Exception;

class NewsController extends Controller
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

    public function init()
    {
        if ($this->page){
            $this->tpl->getBreadcrumbs()->fromSerialize($this->page->get('path'));
        }
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
            throw new Sfcms_Http_Exception($this->t('news','Article not found'), 404);
        }

        // работаем над хлебными крошками
        $this->getTpl()->getBreadcrumbs()
            ->addPiece($news->alias, $news->title);
        $this->request->setTitle($news->title);

        $this->tpl->assign('news', $news);

        if (!$this->auth->hasPermission($news['protected'])) {
            throw new Sfcms_Http_Exception($this->t('Access denied'), 403);
        }
        return $this->tpl->fetch('news.item');
    }

    /**
     * Отображать список новостей на сайте
     * @param NewsModel $model
     * @return mixed
     */
    public function getNewsList(NewsModel $model)
    {
        /** @var Category $category */
        $category = $model->getModel('NewsCategory')->find($this->page['link']);

        if (!$category) {
            return $this->tpl->fetch('news.catempty');
        }

        $cond = '`deleted` = 0 AND `hidden` = 0 AND `cat_id` = ?';
        $params = array($category->getId());

        $count = $model->count($cond, $params);

        $paging = $this->paging($count, $category->per_page, $this->page->alias);

//        $list   = $model->findAllWithLinks(array(
        $list = $model->findAll(array(
                'cond' => $cond,
                'params' => $params,
                'limit' => $paging['limit'],
                'order' => '`date` DESC, `priority` DESC, `id` DESC',
            )
        );

        $this->tpl->assign(array(
            'page' => $this->page,
            'paging' => $paging,
            'list' => $list,
            'cat' => $category,
        ));

        switch ($category['type_list']) {
            case 2:
                $template = 'news.items_list';
                break;
            default:
                $template = 'news.items_blog';
        }

        return $this->tpl->fetch($template);
    }

    /**
     * Управление новостями
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->setTitle($this->t('news','News'));

        /** @var NewsModel $model */
        $model      = $this->getModel('News');
        $category   = $this->getModel('NewsCategory');

        $list   = $category->findAll(array('cond'=>'deleted = 0'));
        return array(
            'list'  => $list,
        );
    }

    /**
     * Список новостей для админки
     * @param int $id
     * @return mixed
     */
    public function listAction($id)
    {
        $this->request->setTitle($this->t('news','News'));
        $model      = $this->getModel('News');

        $count  = $model->count('cat_id = :cat_id', array(':cat_id'=>$id));
        $paging = $this->paging( $count, 20, $this->router->createServiceLink('news','list',array('id'=>$id)));

        $list = $model->findAll(array(
            'cond'      => 'cat_id = :cat_id AND deleted = 0',
            'params'    => array(':cat_id'=>$id),
            'limit'     => $paging->limit,
            'order'     => '`date` DESC, `priority` DESC, `id` DESC',
        ));

        $cat    = $this->getModel('NewsCategory')->find( $id );

        return array(
            'paging'    => $paging,
            'list'      => $list,
            'cat'       => $cat,
        );
    }

    /**
     * Редактрование новости для админки
     * @param int $id
     * @param int $cat
     * @return mixed
     */
    public function editAction($id = null, $cat = null)
    {
        $this->request->setTitle($this->t('news', 'News edit'));
        /** @var $model NewsModel */
        $model = $this->getModel('News');
        /** @var $form Form */
        $form = $model->getForm();
        if (null !== $cat) {
            $form->cat_id = $cat;
        }

        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $obj = $form['id'] ? $model->find($form['id']) : $model->createObject()->markNew();
                $obj->attributes = $form->getData();

                return array('error' => 0, 'msg' => $this->t('Data save successfully'));
            }

            return array('error' => 1, 'msg' => $form->getFeedbackString(), 'errors' => $form->getErrors());
        }

        if ($id) {
            $news = $model->find($id);
            $form->setData($news->getAttributes());
        } else {
            $news = $model->createObject();
        }

        $catObj = null;
        if (isset($news['cat_id']) && $news['cat_id']) {
            $catObj = $model->getModel('NewsCategory')->find($news['cat_id']);
        }
        if (null === $catObj && $cat) {
            $catObj = $model->getModel('NewsCategory')->find($cat);
        }

        return array(
            'form' => $form,
            'cat' => $catObj,
        );
    }

    /**
     * Править категорию для админки
     * @param int $id
     * @return mixed
     */
    public function cateditAction($id)
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
    public function catdeleteAction($id)
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

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->request->setTitle($this->t('News'));
        $model      = $this->getModel('News');
        /** @var $obj News */
        $obj    = $model->find($id);
//        $catId = $obj->cat_id;
        $obj->deleted = 1;
//        $this->reload('news/list', array('id'=>$catId));
        return array('error'=>0,'msg'=>$this->t('news','News was delete'));
    }
}
