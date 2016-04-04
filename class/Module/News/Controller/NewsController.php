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
use Symfony\Component\HttpFoundation\JsonResponse;

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

    /**
     * @return mixed
     */
    public function indexAction()
    {
        /** @var NewsModel $model */
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
    protected function getNews( NewsModel $model )
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
    protected function getNewsList(NewsModel $model)
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
     * @return mixed
     */
    public function listAction()
    {
        $perPage = $this->request->query->get('perpage', 20);
        $model      = $this->getModel('News');

        $orderKey = $this->request->query->get('order_key', 'id');
        $orderDir = $this->request->query->get('order_dir', 'desc');
        if (in_array($orderKey, ['id', 'name', 'date', 'created_at', 'updated_at']) && in_array($orderDir, ['asc', 'desc'])) {
            $order = "`$orderKey` $orderDir";
        } else {
            $order = '`date` DESC, `priority` DESC, `id` DESC';
        }

        $cond = 'deleted = 0';
        $params = array();

        if ($this->request->query->get('name')) {
            $cond .= ' AND name LIKE :name';
            $params[':name'] = '%' . $this->request->query->get('name') . '%';
        }
        if ($this->request->query->get('note')) {
            $cond .= ' AND note LIKE :note';
            $params[':note'] = '%' . $this->request->query->get('note') . '%';
        }

        if ($this->request->query->get('id')) {
            $cond .= ' AND id = :id';
            $params[':id'] = $this->request->query->get('id');
        }

        if ($this->request->query->get('cat_id')) {
            $cond .= ' AND cat_id = :cat_id';
            $params[':cat_id'] = $this->request->query->get('cat_id');
        }

        $count  = $model->count($cond, $params);
        $paging = $this->paging($count, $perPage, $this->router->createServiceLink('news', 'list', ['deleted' => 0]));

        $list = $model->findAll([
            'cond'      => $cond,
            'params'    => $params,
            'limit'     => $paging->limit,
            'order'     => $order,
        ]);

        $response = new JsonResponse([
            'data' => array_map(function(News $news){
                return [
                    'id' => intval($news->id),
                    'cat_id' => intval($news->cat_id),
                    'name' => $news->name,
                    'note' => $news->note,
                    'category' => $news->Category ? [
                        'id' => $news->Category->id,
                        'name' => $news->Category->name,
                    ] : null,
                    'main' => $news->main,
                    'hidden' => $news->hidden,
                    'protected' => $news->protected,
                    'date' => (new \DateTime())->setTimestamp($news->date)->format('Y-m-d'),
                    'created_at' => $news->created_at,
                    'updated_at' => $news->updated_at,
                ];
            }, iterator_to_array($list)),
            //'links'    => $paging,
            'page' => $paging->page,
            'records' => $paging->count,
            'total' => $paging->pages,
        ]);

        return $response;
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
     * @param int $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $model = $this->getModel('News');
        /** @var $obj News */
        $obj = $model->find($id);
//        $catId = $obj->cat_id;
        $obj->deleted = 1;
//        $this->reload('news/list', array('id'=>$catId));
        return array('error'=>0,'msg'=>$this->t('news','News was delete'));
    }
}
