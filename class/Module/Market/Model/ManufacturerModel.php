<?php
/**
 * Модель Manufacturers
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms_Model;
use Data_Collection;
use Forms_Manufacturers_Edit;

class ManufacturerModel extends Sfcms_Model
{

    public function tableClass()
    {
        return 'Data_Table_Manufacturers';
    }

    public function objectClass()
    {
        return 'Data_Object_Manufacturers';
    }

    /**
     * Вернет коллекцию производителей, подходящую под список id категорий
     * @param array $catIds
     * @return Data_Collection|null
     */
    public function findAllByCatalogCategories( array $catIds )
    {
        if ( 0 == count($catIds) ) {
            return $this->createCollection();
        }
        $catalogTable   = $this->getModel('Catalog')->getTableName();
        $manufTable     = $this->getTableName();
        $sql = sprintf('SELECT m.* FROM `%s` m '
                . 'INNER JOIN `%s` c ON '
                    . 'c.manufacturer = m.id AND c.deleted = 0 AND c.hidden = 0 AND c.cat = 0 AND c.parent IN ('.join(',',$catIds).')'
                . ' GROUP BY m.id', $manufTable, $catalogTable );
        $manufList  = $this->db->fetchAll( $sql );
        $collection = $this->createCollection( $manufList );
//        $this->log( $collection );
        return $collection;
    }


    /**
     * @return Forms_Manufacturers_Edit
     */
    public function getForm()
    {
        return new Forms_Manufacturers_Edit();
    }
}