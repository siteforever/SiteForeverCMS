<?php
/**
 * Модель Material
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms\Data\Collection;
use Sfcms\Model;
use Sfcms\JqGrid\Provider;
use Forms\Material\Edit as FormEdit;

class MaterialModel extends Model
{

    /**
     * Вернет коллекцию материалов, подходящую под список id категорий
     * @param array $catIds
     * @return Collection|null
     */
    public function findAllByCatalogCategories( array $catIds )
    {
        if ( 0 == count($catIds) ) {
            return $this->createCollection();
        }
        $catalogTable   = $this->getModel('Catalog')->getTable();
        $materialTable     = $this->getTable();
        $sql = sprintf('SELECT m.* FROM `%s` m '
            . 'INNER JOIN `%s` c ON '
            . 'c.material = m.id AND c.deleted = 0 AND c.hidden = 0 AND c.cat = 0 AND c.parent IN ('.join(',',$catIds).')'
            . ' GROUP BY m.id', $materialTable, $catalogTable );
        $materialList  = $this->db->fetchAll( $sql );
        $collection = $this->createCollection( $materialList );
//        $this->log( $collection );
        return $collection;
    }


    public function getForm()
    {
        return new FormEdit();
    }

    /**
     * @return Provider
     */
    public function getProvider($request)
    {
        $provider = new Provider($request);
        $provider->setModel( $this );

        $criteria = $this->createCriteria();

        $provider->setCriteria( $criteria );

        $provider->setFields(array(
            'id'    => array(
                'title' => 'Id',
                'width' => 50,
            ),
            'image' => array(
                'width' => 80,
                'sortable' => false,
                'format' => array(
                    'image' => array('width'=>50,'height'=>50),
                    'link' => array('controller'=>'material', 'action'=>'edit','id'=>':id','class'=>'edit','title'=>':name'),
                ),
            ),
            'name'  => array(
                'title' => t('material','Name'),
                'width' => 200,
                'format' => array(
                    'link' => array('controller'=>'material', 'action'=>'edit','id'=>':id','class'=>'edit','title'=>':name'),
                ),
            ),
            'active' => array(
                'title' => t('material','Active'),
                'width' => 50,
                'format' => array(
                    'bool' => array(),
                ),
            ),
        ));

        return $provider;
    }
}
