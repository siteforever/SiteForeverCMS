<?php
/**
 * Контроллер управления доставкой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

class Controller_Delivery extends \Sfcms_Controller
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
        $items = $model->findAll(array('order'=>'pos'));
        return array(
            'items' => $items,
        );
    }

    /**
     * @param int $id
     * @return array|string
     */
    public function editAction( $id )
    {
        $form = new Forms_Delivery_Edit();
        $model = $this->getModel();

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
     * @param int $type
     * @return array
     */
    public function selectAction( $type )
    {
        $model = $this->getModel();
        /** @var $delivery Data_Object_Delivery */
        $delivery = $model->find( $type );
        if ( $delivery ){
            $_SESSION['delivery'] = $type;
            $basketSum = $this->getBasket()->getSum();
            return array(
                'error'=> 0,
                'msg'  => 'ok',
                'cost' => number_format( $delivery->cost, 2, ',', '' ),
                'sum'  => number_format( $delivery->cost + $basketSum, 2, ',', '' )
            );
        }
        return array('error'=>1,'msg'=>'Delivery not found');
    }

    /**
     * @param array $sort
     * @return string
     */
    public function sortableAction( $sort = array() )
    {
        try {
            $deliveries = $this->getModel()->findAll( ' id IN (' . implode( ',', $sort ) . ')' );
            $sort       = array_flip( $sort );
            foreach ( $deliveries as $delivery ) {
                $delivery->pos = $sort[ $delivery->id ];
            }
        } catch ( Exception $e ) {
            return $e->getMessage();
        }

        return 'ok';
    }
}
