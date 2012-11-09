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

class GoodsController extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setTitle(t('Goods'));
    }

    public function access()
    {
        return array(
            'system' => array('admin'),
        );
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        // TODO: Implement indexAction() method.
    }


    public function searchAction()
    {
        $query = filter_var($this->request->get('q'));
        $query = urldecode( $query );

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


    public function adminAction()
    {
        $model = $this->getModel('Catalog');
        $count = $model->count('cat=?', array(0));
        $pager = $this->paging( $count, 25, 'goods/admin' );
        $goods = $model->with('Category','Manufacturer')->findAll('cat=?', array(0), 'name', $pager->limit);

        return array(
            'list'=>$goods,
            'pager'=>$pager,
        );
    }
}
