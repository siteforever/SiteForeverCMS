<?php
namespace Module\Catalog\Controller;
/**
 * Типы товаров
 * @author: keltanas
 * @link  http://siteforever.ru
 */
use Sfcms\Controller;
use Module\Catalog\Model\CatalogModel;
use Forms\Prodtype\Edit as  FormEdit;
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
        $this->request->setTitle(t('catalog','Product types'));
        /** @var $model CatalogModel */
        $model = $this->getModel('ProductType');
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
        /** @var $model CatalogModel */
        $model = $this->getModel('ProductType');
        $provider = $model->getProvider();
        return $provider->getJsonData();
    }

    /**
     * Форма правки товара
     * @param int $id
     * @return string
     */
    public function editAction( $id )
    {
        $model = $this->getModel('Producttype');
        $obj = $id ? $model->find( $id ) : $model->createObject();
        $form = new FormEdit();
        $form->setData( $obj->attributes );
        return array(
            'form'=>$form,
            'fields'=>$obj->Fields,
            'types' => array(
                'string'    => t('catalog','String'),
                'text'      => t('catalog','Text'),
                'int'       => t('catalog','Int'),
                'datetime'  => t('catalog','Datetime'),
            )
        );
    }

    /**
     * Сохранение товара
     * @return array
     */
    public function saveAction()
    {
        $model = $this->getModel('Producttype');
        $form = new FormEdit();
        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $form->id ? $model->find($form->id) : $model->createObject();
                $obj->setAttributes( $form->getData() );
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

                return array('error'=>0,'msg'=>t('Data save successfully')) + ( isset( $fields ) ? array('fields'=>$fields) : array() );
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
