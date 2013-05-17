<?php
/**
 * Контроллер производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Market\Controller;

use Sfcms\Controller;
use Sfcms\Request;
use Module\Market\Model\MaterialModel;

class MaterialController extends Controller
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
        /** @var $model MaterialModel */
        $model = $this->getModel('Material');
        $provider = $model->getProvider($this->request);
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
        /** @var $model MaterialModel */
        $model = $this->getModel('Material');
        $provider = $model->getProvider($this->request);
        return $provider->getJsonData();
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function editAction($id)
    {
        $this->request->setTitle( t('material','Materials') );

        /** @var $model MaterialModel */
        $model = $this->getModel( 'Material' );
        $form  = $model->getForm();

        if ( $id ) {
            $obj = $model->find( $id );
            $form->setData( $obj->getAttributes() );
        }

        return array( 'form'=> $form );
    }

    public function saveAction()
    {
        $this->request->setTitle( t( 'Materials' ) );

        /** @var $model MaterialModel */
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
