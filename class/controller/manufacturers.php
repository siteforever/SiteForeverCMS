<?php
/**
 * Контроллер производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Controller_Manufacturers extends Sfcms_Controller
{
    /**
     * @return array
     */
   public function access()
   {
       return array(
           'system'    => array('admin','edit','save','delete'),
       );
   }

   public function defaults()
   {
       return array(
           'manufacturers',
           array(
               'onPage' => 10,
           ),
       );
   }


    /**
     * Index Action
     */
    public function indexAction()
    {
        $model  = $this->getModel();
        $id     = $this->request->get('id');

        if ( $id ) {
            /** @var $item Data_Object_Manufacturers */
            $item = $model->find( $id );
            $this->request->setTitle( $item->name );
            $this->tpl->getBreadcrumbs()
                ->addPiece(null,$this->request->getTitle());
            $this->tpl->assign('item', $item);
            return $this->tpl->fetch('manufacturers/view');
        }

        $count = $model->count();
        $paging = $this->paging(
            $count,
            $this->config->get('catalog.onPage'),
            $this->getPage()->getUrl()
        );


        $items = $model->findAll(array('limit'=>$paging->limit));
        return array(
            'items' => $items,
        );
    }


    public function adminAction()
    {
        $this->request->setTitle(t('Manufacturers'));

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
        $this->request->setTitle(t('Manufacturers'));

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
        $this->request->setTitle(t('Manufacturers'));

        /** @var $model Model_Manufacturers */
        $model = $this->getModel();
        $form  = $model->getForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                $obj->save();
                return array('error'=>0,'msg'=>t('Data save successfully'));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }
        return t('Form not posted');
    }


    public function deleteAction()
    {
        $this->request->setTitle(t('Manufacturers'));

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
