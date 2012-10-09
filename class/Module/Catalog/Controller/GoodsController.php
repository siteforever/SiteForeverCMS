<?php
/**
 * Товары
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms_Controller;

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
