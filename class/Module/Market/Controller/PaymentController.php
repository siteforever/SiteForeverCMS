<?php
/**
 * Контроллер оплаты
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Module\Market\Controller;

use Sfcms_Controller;

use Forms\Payment\Edit as FormEdit;

class PaymentController extends Sfcms_Controller
{
    public function access()
    {
        return array(
            'system' => array('admin','edit','delete'),
        );
    }

    public function adminAction()
    {
        $this->request->setTitle(t('Payment'));
        $model = $this->getModel();
        $list = $model->findAll();
        return array(
            'list' => $list,
        );
    }

    /**
     * @param int $id
     * @return array
     */
    public function editAction( $id )
    {
        $model = $this->getModel();
        $form = new FormEdit();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $payObj = $form->id ? $model->find($form->id) : $model->createObject();
                $payObj->attributes = $form->getData();
                return array('error'=>0,'msg'=>t('Data save successfully'));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString(),'errors'=>$form->getErrors());
            }
        }

        $payObj = $id ? $model->find( $id ) : $model->createObject();
        $form->setData( $payObj->attributes );

        return array(
            'form' => $form,
            'obj' => $payObj,
        );
    }

    /**
     * @param int $id
     * @return array
     */
    public function deleteAction( $id )
    {
        if ( $id ) {
            $this->getModel('Payment')->delete( $id );
            return array('id' => $id);
        }
        return array('error'=>1);
    }
}
