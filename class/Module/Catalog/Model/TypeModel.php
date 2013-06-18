<?php
/**
 * Модель Product_Type
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Catalog\Model;

use Sfcms\JqGrid\Provider;
use Sfcms\Model;

class TypeModel extends Model
{
    public function relation()
    {
        return array(
            'Fields' => array(self::HAS_MANY, 'ProductField', 'product_type_id'),
        );
    }

    /**
     * @return Provider
     */
    public function getProvider($request)
    {
        $provider = new Provider( $request );
        $provider->setModel( $this );

        $criteria = $this->createCriteria();
//        $criteria->condition = 'cat = 0 AND deleted = 0';

        $provider->setCriteria( $criteria );

        $provider->setFields(array(
            'id'    => array(
                'title' => 'Id',
                'width' => 50,
            ),
            'name'  => array(
                'title' => $this->t('catalog','Name'),
                'width' => 200,
                'format' => array(
                    'link' => array('class'=>'edit', 'controller'=>'prodtype', 'action'=>'edit','id'=>':id','title'=>$this->t('Edit').' :name'),
                ),
            ),
            'delete' => array(
                'title' => $this->t('Delete'),
                'width' => 50,
                'value' => 'delete',
                'format' => array(
                    'link' => array('class'=>'delete', 'controller'=>'prodtype','action'=>'delete','id'=>':id'),
                ),
            ),
        ));

        return $provider;
    }
}
