<?php
/**
 * Товары
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms\Request;
use Sfcms\Controller;
use Module\Catalog\Model\CatalogModel;
use Sfcms\JqGrid\Provider;
use Sfcms\Yandex\Yml;

class GoodsController extends Controller
{
    public function init()
    {
        $this->request->setTitle($this->t('Goods'));
    }

    public function access()
    {
        return array(
            USER_ADMIN => array('admin','grid','edit'),
        );
    }


    /**
     * Поиск товаров
     * @return array
     */
    public function searchAction()
    {
        $query = filter_var($this->request->get('q'));
        $query = trim( urldecode( $query ) );

        if ( strlen( $query ) < 2 ) {
            return 'Запрос слишком короткий';
        }

        $this->request->setTitle($this->t('goods','Goods search'));
        $this->getTpl()->getBreadcrumbs()->addPiece('index',$this->t('Home'))->addPiece(null, $this->request->getTitle());

        /** @var $modelCatalog CatalogModel */
        $modelCatalog  = $this->getModel('Catalog');

        $crit = $modelCatalog->createCriteria();
        $crit->condition = '`deleted`=0 AND `hidden`=0 AND `protected`=0 AND `cat`=0 AND `absent`!=1 AND '
            .'( `name` LIKE ? OR `text` LIKE ? )';
        $crit->params = array("%$query%", "%$query%", );

        $count = $modelCatalog->count($crit);
        $paging = $this->paging($count, 10, 'goods/search?q='.urlencode($query));

        $crit->limit = $paging->limit;
        $crit->from = $paging->from;
        $crit->order = '`top` DESC';
        $goods = $modelCatalog->findAll($crit);

        return $this->render('goods.search', array(
            'query' => $query,
            'goods' => $goods,
            'paging' => $paging,
        ));
    }


    /**
     * Админка с использованием jqGrid
     */
    public function adminAction()
    {
        /** @var $model CatalogModel */
        $model = $this->getModel('Catalog');
        $provider = $model->getProvider($this->request);

        return array(
            'provider'      => $provider,
            'category'      => $this->request->getSession()->get('category', 0),
            'type'          => $this->request->getSession()->get('type', -1),
        );
    }

    /**
     * Реакция на аяксовый запрос от jqGrid
     * @return string
     */
    public function gridAction()
    {
        /** @var $model CatalogModel */
        $model = $this->getModel('Catalog');
        $provider = $model->getProvider($this->request);
        return $provider->getJsonData();
    }

    /**
     * @param int $id
     * @return string
     */
    public function editAction($id)
    {
        if (!$id) {
            return 'id not defined';
        }
        /** @var $model CatalogModel */
        $model = $this->getModel('Catalog');
        $product = $model->find( $id );
        $form = $model->getForm();
        $form->setData( $product->attributes );
        return $form->html(false,false);
    }

    /**
     * YandexML export
     * @return string
     */
    public function ymlAction()
    {
        $model = $this->getModel('Catalog');
        $products = $model->findAll('cat = 0 AND hidden = 0 AND deleted = 0 AND protected = 0');
        $categories = $model->findAll('cat = 1 AND hidden = 0 AND deleted = 0 AND protected = 0');

        $yml = new Yml( $this->app() );
        $yml->setCollection( $products );
        $yml->setCategories( $categories );

        $this->request->setAjax(true, Request::TYPE_XML);
        return $yml->output($this->request);
    }
}
