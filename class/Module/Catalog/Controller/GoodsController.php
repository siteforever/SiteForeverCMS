<?php
/**
 * Товары
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms\Request;
use Sfcms_Controller;
use Module\Catalog\Model\CatalogModel;
use Sfcms\JqGrid\Provider;
use Sfcms\Yandex\Yml;

class GoodsController extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setTitle(t('Goods'));
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

        $this->request->setTitle(t('goods','Goods search'));
        $this->getTpl()->getBreadcrumbs()->addPiece('index',t('Home'))->addPiece(null, $this->request->getTitle());

        /** @var CatalogModel */
        $modelCatalog  = $this->getModel('Catalog');

        $goods  = $modelCatalog->findGoodsByQuery( $query );

        return array(
            'query' => $query,
            'goods' => $goods,
        );
    }


    /**
     * Админка с использованием jqGrid
     */
    public function adminAction()
    {
        /** @var $model CatalogModel */
        $model = $this->getModel('Catalog');
        $provider = $model->getProvider();

//        $categories = $model->getCategoryList();
        /** @var $typeModel  */
//        $typeModel     = $this->getModel( 'ProductType' );
//        $types         = $typeModel->findAll( array('order'=>'name') );
//        $typesArray    = array(-1=>'Все типы','Без типа') + $types->column('name');

        return array(
            'provider'      => $provider,
//            'categories'    => $categories,
            'category'      => $this->app()->getSession()->get('category') ?: 0,
//            'types'         => $typesArray,
            'type'          => $this->app()->getSession()->get('type') ?: -1,
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
        $provider = $model->getProvider();
        return $provider->getJsonData();
    }

    /**
     * @param int $id
     */
    public function editAction( $id )
    {
        if ( ! $id ) {
            return 'id not defined';
        }
        /** @var $model CatalogModel */
        $model = $this->getModel('Catalog');
        $product = $model->find( $id );
        $form = $model->getForm();
        $form->setData( $product->attributes );
        return $form->html(false,false);
    }

    public function ymlAction()
    {
        $model = $this->getModel('Catalog');
        $products = $model->findAll('cat = 0 AND hidden = 0 AND deleted = 0 AND protected = 0');
        $categories = $model->findAll('cat = 1 AND hidden = 0 AND deleted = 0 AND protected = 0');

        $yml = new Yml( $this->app() );
        $yml->setCollection( $products );
        $yml->setCategories( $categories );

        $this->request->setAjax(true, Request::TYPE_XML);
        return $yml->output();
    }
}
