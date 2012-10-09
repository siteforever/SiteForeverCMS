<?php
/**
 * Контроллер управления доставкой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Index\Delivery;

class Controller extends \Sfcms_Controller
{
    public function access()
    {
        return array(
            'system' => array('admin','edit'),
        );
    }


    public function adminAction()
    {
        $this->request->setTitle(t('delivery','Delivery'));
        $model = $this->getModel();
        $items = $model->findAll();
        return array(
            'items' => $items,
        );
    }

    public function editAction()
    {
        $form = new \Forms_Delivery_Edit();
        $model = $this->getModel();

        $id = $this->request->get('id',\Request::INT);

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
     * Выбор способа доставки
     * @return array
     */
    public function selectAction()
    {
        $type = $this->request->get('type', \Request::INT);
        $model = $this->getModel();
        $delivery = $model->find( $type );
        if ( $delivery ){
            $_SESSION['delivery']['type'] = $type;
            $_SESSION['delivery']['cost'] = $delivery->cost;
            $basketSum = $this->getBasket()->getSum();
            return array(
                'error'=>0,
                'msg'=>'ok',
                'cost'=>number_format($delivery->cost,2,',',' '),
                'sum'=>number_format( $delivery->cost + $basketSum, 2, ',', ' ' )
            );
        }
        return array('error'=>1,'msg'=>'Delivery not found');
    }
}
