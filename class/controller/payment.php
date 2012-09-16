<?php
/**
 * Контроллер оплаты
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

class Controller_Payment extends \Sfcms_Controller
{
    public function access()
    {
        return array(
            'system' => array('admin','edit'),
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
     * @throws RuntimeException
     */
    public function editAction( $id )
    {
        $model = $this->getModel();
        $form = new \Forms\Payment\Edit();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $payObj = $form['id'] ? $model->find($form['id']) : $model->createObject();
                $payObj->attributes = $form->getData();
                return array('error'=>0,'msg'=>t('Data save successfully'));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString(),'errors'=>$form->getErrors());
            }
        }

        $payObj = $model->find( $id );
        $form->setData( $payObj->attributes );

        return array(
            'form' => $form,
            'obj' => $payObj,
        );
    }
}
