<?php
/**
 * Контроллер управления доставкой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Market\Controller;

use Sfcms_Controller;
use Data_Object_Delivery;
use Request;
use Forms_Delivery_Edit;

class DeliveryController extends Sfcms_Controller
{
    public function access()
    {
        return array(
            'system' => array('admin','edit','sortable'),
        );
    }


    public function adminAction()
    {
        $this->request->setTitle(t('delivery','Delivery'));
        $model = $this->getModel('Delivery');
        $items = $model->findAll(array('order'=>'pos'));
        return array(
            'items' => $items,
        );
    }


    public function editAction()
    {
        $form = new Forms_Delivery_Edit();
        $model = $this->getModel('Delivery');

        $id = $this->request->get('id',Request::INT);

        if ( $id ) {
            $obj = $model->find( $id );
            $form->setData( $obj->attributes );
        }

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                if ( $id = $form->getField('id')->getValue() ) {
                    $obj = $model->find( $id );
                } else {
                    $obj = $model->createObject();
                }
                $obj->attributes = $form->getData();
                $obj->markDirty();
                return array('error'=>0,'msg'=>t('Data save successfully'));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }

        return $form->html(false,false);
    }


    /**
     * Пересортировака порядка доставки
     * @param array $sort
     */
    public function sortableAction( $sort )
    {
        $model  = $this->getModel('Delivery');
        $items  = $model->findAll( sprintf('id IN (%s)', join(',', $sort)) );
        $sort   = array_flip( $sort );
        /** @param $item Data_Object_Delivery */
        foreach( $items as $item ) {
            $item->pos = $sort[ $item->id ];
        }
    }


    /**
     * Выбор способа доставки
     * @return array
     */
    public function selectAction()
    {
        $type = $this->request->get('type', Request::INT);
        try {
            $delivery = $this->app()->getDelivery();
            $delivery->setType( $type );
            $basketSum = $this->getBasket()->getSum();
            return array(
                'error' => 0,
                'msg'   => 'ok',
                'cost'  => number_format($delivery->cost(),2,',',' '),
                'sum'   => number_format( $delivery->cost() + $basketSum, 2, ',', ' ' )
            );
        } catch ( \Sfcms\Exception $e ) {
            return array('error'=>$e->getCode(),'msg'=>$e->getMessage());
        }
    }
}
