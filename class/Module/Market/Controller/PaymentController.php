<?php
/**
 * Контроллер оплаты
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Module\Market\Controller;

use Sfcms\Controller;

use Forms\Payment\Edit as FormEdit;

class PaymentController extends Controller
{
    public function access()
    {
        return array(
            'system' => array('admin','edit','delete'),
        );
    }

    public function adminAction()
    {
        $this->request->setTitle($this->t('Payment'));
        $model = $this->getModel('Payment');
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
        $model = $this->getModel('Payment');
        $form = new FormEdit();

        if ( $form->getPost($this->request) ) {
            if ( $form->validate() ) {
                $payObj = $form->id ? $model->find($form->id) : $model->createObject()->markNew();
                $payObj->attributes = $form->getData();
                return array('error'=>0,'msg'=>$this->t('Data save successfully'));
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
