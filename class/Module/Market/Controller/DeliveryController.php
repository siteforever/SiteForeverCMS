<?php
/**
 * Контроллер управления доставкой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Market\Controller;

use Sfcms\Controller;
use Module\Market\Object\Delivery;
use Sfcms\Request;
use Module\Market\Form\DeliveryEditForm;

class DeliveryController extends Controller
{
    public function access()
    {
        return array(
            'system' => array('admin','edit','sortable'),
        );
    }


    public function adminAction()
    {
        $this->request->setTitle($this->t('delivery','Delivery'));
        $model = $this->getModel('Delivery');
        $items = $model->findAll(array('order'=>'pos'));
        return array(
            'items' => $items,
        );
    }


    /**
     * @param int $id
     *
     * @return array|string
     */
    public function editAction($id)
    {
        $form = new DeliveryEditForm();
        $model = $this->getModel('Delivery');

        if ($id) {
            $obj = $model->find($id);
            $form->setData($obj->attributes);
        }

        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                if ($id = $form->getChild('id')->getValue()) {
                    $obj = $model->find($id);
                } else {
                    $obj = $model->createObject()->markNew();
                }
                $obj->attributes = $form->getData();
                $obj->markDirty();
                return array('error' => 0, 'msg' => $this->t('Data save successfully'));
            } else {
                return array('error' => 1, 'msg' => $form->getFeedbackString());
            }
        }

        return $form->html(false, false);
    }


    /**
     * Пересортировака порядка доставки
     */
    public function sortableAction()
    {
        $sort = $this->request->get('sort');
        $model  = $this->getModel('Delivery');
        $items  = $model->findAll( sprintf('id IN (%s)', join(',', $sort)) );
        $sort   = array_flip( $sort );
        /** @param $item Delivery */
        foreach( $items as $item ) {
            $item->pos = $sort[ $item->id ];
        }
    }
}
