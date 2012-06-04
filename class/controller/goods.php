<?php
/**
 * Товары
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Controller_Goods extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setTitle(t('Goods'));
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
