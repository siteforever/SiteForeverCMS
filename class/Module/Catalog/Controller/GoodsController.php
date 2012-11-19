<?php
/**
 * Товары
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms_Controller;
use Model_Catalog;
use Sfcms\JqGrid\Provider;

class GoodsController extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setTitle(t('Goods'));
    }

    public function access()
    {
        return array(
            USER_ADMIN => array('admin','jqgrid'),
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

        /** @var Model_Catalog */
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
        /** @var $model Model_Catalog */
        $model = $this->getModel('Catalog');
        $provider = $model->getProvider();
        return array(
            'provider' => $provider,
        );
    }

    /**
     * Реакция на аяксовый запрос от jqGrid
     * @return string
     */
    public function jqgridAction()
    {
        /** @var $model Model_Catalog */
        $model = $this->getModel('Catalog');
        $provider = $model->getProvider();
        return $provider->getJsonData();
    }
}
