<?php
/**
 * Контроллер производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Market\Controller;

use Sfcms_Controller;
use Request;
use Model_Material;

class MaterialController extends Sfcms_Controller
{
    public function access()
    {
        return array(
            USER_ADMIN => array('admin','grid','edit','save','delete'),
        );
    }


    public function indexAction()
    {
        $model = $this->getModel('Material');
        $list  = $model->findAll('active = 1');
        return array(
            'list' => $list,
        );
    }

    public function adminAction()
    {
        $this->request->setTitle(t('material','Materials'));
        /** @var $model Model_Material */
        $model = $this->getModel('Material');
        $provider = $model->getProvider();
        return array(
            'provider' => $provider,
        );

    }

    /**
     * Реакция на аяксовый запрос от jqGrid
     * @return string
     */
    public function gridAction()
    {
        /** @var $model Model_Material */
        $model = $this->getModel('Material');
        $provider = $model->getProvider();
        return $provider->getJsonData();
    }

    public function editAction()
    {
        $this->request->setTitle( t('material','Materials') );

        /** @var $model Model_Material */
        $model = $this->getModel( 'Material' );
        $form  = $model->getForm();

        $id = $this->request->get( 'id', Request::INT );

        if ( $id ) {
            $obj = $model->find( $id );
            $form->setData( $obj->getAttributes() );
        }

        return array( 'form'=> $form );
    }

    public function saveAction()
    {
        $this->request->setTitle( t( 'Materials' ) );

        /** @var $model Model_Material */
        $model = $this->getModel( 'Material' );
        $form  = $model->getForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject( $form->getData() );
                $obj->save();
                return array( 'error'=> 0, 'msg'=> t( 'Data save successfully' ) );
            } else {
                return array( 'error'=> 1, 'msg'=> $form->getFeedbackString() );
            }
        }
        return t( 'Form not posted' );
    }



}
