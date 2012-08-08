<?php
/**
 * Контроллер производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Controller_Manufacturers extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setTitle(t('Manufacturers'));
    }

    /**
     * @return array
     */
   public function access()
   {
       return array(
           'system'    => array('admin','edit','save','delete'),
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
        $this->app()->addScript('/misc/admin/manufacturers.js');
        /** @var $model Model_Manufacturers */
        $model = $this->getModel();
        $count = $model->count();
        $paging = $this->paging( $count, 20, 'manufacturers/admin' );

        $rows = $model->findAll(array('limit'=>$paging->limit));
        return array('rows'=>$rows, 'paging'=>$paging);
    }


    public function editAction()
    {
        /** @var $model Model_Manufacturers */
        $model = $this->getModel();
        $form  = $model->getForm();

        $id = $this->request->get('id', Request::INT);

        if ( $id ) {
            $obj = $model->find( $id );
            $form->setData( $obj->getAttributes() );
        }

        return array( 'form'=>$form );
    }


    public function saveAction()
    {
        /** @var $model Model_Manufacturers */
        $model = $this->getModel();
        $form  = $model->getForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                if ( ! $obj->getId() ) {
                    $obj->save();
                }
                $this->reload('manufacturers/admin', array(), 2000, true);
                return t('Data save successfully');
            } else {
                return $form->getFeedbackString();
            }
        }
        return t('Form not posted');
    }


    public function deleteAction()
    {
        $id = $this->request->get('id', Request::INT);
        /** @var $model Model_Manufacturers */
        $model = $this->getModel();
        /** @var $obj Data_Object_Manufacturers */
        $obj = $model->find($id);
        $name = $obj->name;
        $model->delete( $id );
        return $name . ' ' . t('delete success');
    }
}
