<?php
/**
 * Контроллер производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Market\Controller;

use Sfcms_Controller;
use Sfcms\Request;
use Module\Market\Model\ManufacturerModel;
use Module\Market\Object\Manufacturer;

class ManufacturerController extends Sfcms_Controller
{
    /**
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array( 'admin', 'edit', 'save', 'delete' ),
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
        $model = $this->getModel( 'Manufacturers' );
        $id    = $this->request->get( 'id' );

        if ( $id ) {
            /** @var $item Manufacturer */
            $item = $model->find( $id );
            $this->request->setTitle( $item->name );
            $this->getTpl()->getBreadcrumbs()
                ->addPiece( null, $this->request->getTitle() );
            $this->tpl->assign( 'item', $item );
            return $this->tpl->fetch( 'manufacturers/view' );
        }

        $count  = $model->count();
        $paging = $this->paging(
            $count,
            $this->config->get( 'catalog.onPage' ),
            $this->getPage()->getUrl()
        );


        $items = $model->findAll( array( 'limit'=> $paging->limit ) );
        return array(
            'items' => $items,
        );
    }


    public function adminAction()
    {
        $this->request->setTitle( t( 'Manufacturers' ) );

        $this->app()->addScript( '/misc/admin/manufacturers.js' );

        /** @var $model ManufacturerModel */
        $model  = $this->getModel( 'Manufacturers' );
        $count  = $model->count();
        $paging = $this->paging( $count, 20, 'manufacturers/admin' );

        $rows = $model->findAll( array( 'limit'=> $paging->limit ) );
        return array( 'rows'=> $rows, 'paging'=> $paging );
    }


    /**
     * @param int $id
     *
     * @return array
     */
    public function editAction($id)
    {
        $this->request->setTitle( t( 'Manufacturers' ) );

        /** @var $model ManufacturerModel */
        $model = $this->getModel( 'Manufacturers' );
        $form  = $model->getForm();

        if ( $id ) {
            $obj = $model->find( $id );
            $form->setData( $obj->getAttributes() );
        }

        return array( 'form'=> $form );
    }


    public function saveAction()
    {
        $this->request->setTitle( t( 'Manufacturers' ) );

        /** @var $model ManufacturerModel */
        $model = $this->getModel( 'Manufacturers' );
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


    /**
     * @param int $id
     *
     * @return string
     */
    public function deleteAction($id)
    {
        $this->request->setTitle( t( 'Manufacturers' ) );
        /** @var $model ManufacturerModel */
        $model = $this->getModel( 'Manufacturers' );
        /** @var $obj ManufacturerModel */
        $obj  = $model->find( $id );
        $name = $obj->name;
        $model->delete( $id );
        return $name . ' ' . t( 'delete success' );
    }
}
