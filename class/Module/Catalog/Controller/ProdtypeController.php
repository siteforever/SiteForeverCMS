<?php
namespace Module\Catalog\Controller;
/**
 * Типы товаров
 * @author: keltanas
 * @link  http://siteforever.ru
 */
use Sfcms\Controller;
use Module\Catalog\Model\CatalogModel;
use Module\Catalog\Form\ProdtypeForm;
use Module\Catalog\Model\FieldModel;

class ProdtypeController extends Controller
{
    public function access()
    {
        return array(
            USER_ADMIN => array('admin','grid','edit','save','deleteField',),
        );
    }

    /**
     * Админка с использованием jqGrid
     */
    public function adminAction()
    {
        $this->request->setTitle($this->t('catalog','Product types'));
        /** @var $model CatalogModel */
        $model = $this->getModel('ProductType');
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
        /** @var $model CatalogModel */
        $model = $this->getModel('ProductType');
        $provider = $model->getProvider($this->request);
        return $provider->getJsonData();
    }

    /**
     * Форма правки товара
     * @param int $id
     * @return string
     */
    public function editAction($id = null)
    {
        $model = $this->getModel('ProductType');
        $obj = null !== $id ? $model->find($id) : $model->createObject();
        $form = new ProdtypeForm();
        $form->setData($obj->attributes);
        return array(
            'form'   => $form,
            'fields' => $obj->id ? $obj->Fields : null,
            'types'  => array(
                'string'    => $this->t('catalog','String'),
                'text'      => $this->t('catalog','Text'),
                'int'       => $this->t('catalog','Int'),
                'datetime'  => $this->t('catalog','Datetime'),
            )
        );
    }

    /**
     * Сохранение товара
     * @return array
     */
    public function saveAction()
    {
        $model = $this->getModel('ProductType');
        $form = new ProdtypeForm();
        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $obj = $form['id'] ? $model->find($form['id']) : $model->createObject();
                $obj->setAttributes($form->getData());
                $obj->save();

                if ( $obj->getId() && isset($_POST['field']['id']) && is_array($_POST['field']['id']) ) {
                    /** @var $pfModel FieldModel */
                    $pfModel = $this->getModel('ProductField');

                    $fields = array_filter( array_map(function( $i ) use ( $pfModel, $obj ) {
                        if ( ! ( isset( $_POST['field']['name'][$i] ) && trim( $_POST['field']['name'][$i] ) ) ) {
                            return false;
                        }
                        $field = array(
                            'id'    => $_POST['field']['id'][$i],
                            'name'  => $_POST['field']['name'][$i],
                            'type'  => $_POST['field']['type'][$i],
                            'unit'  => $_POST['field']['unit'][$i],
                            'product_type_id' => $obj->getId(),
                        );
                        $objField = $field['id'] ? $pfModel->find( $field['id'] ) : $pfModel->createObject();
                        if ( $objField ) {
                            $objField->setAttributes( $field );
                            if ( ! $objField->id ) {
                                $objField->save();
                            }
                            return $objField->attributes;
                        } else {
                            return $field;
                        }
                    }, array_keys( $_POST['field']['id'] ) ) );
                }

                return array('error'=>0,'msg'=>$this->t('Data save successfully')) + ( isset( $fields ) ? array('fields'=>$fields) : array() );
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString(),'errors'=>$form->getFeedback());
            }
        }
        return array('error'=>1,'msg'=>'Data not defined');
    }

    /**
     * Удалить поле
     * @param int $id
     *
     * @return array
     */
    public function deleteFieldAction( $id )
    {
        $pfModel = $this->getModel('ProductField');
        if ( $id ) {
            $obj = $pfModel->find( $id );
            if ( $obj ) {
                $obj->markDeleted();
                return array('error'=>0,'msg'=>'Удалено');
            }
        }
        return array('error'=>1,'msg'=>'Ошибка удаления');
    }


}
