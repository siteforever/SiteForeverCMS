<?php
/**
 * Модель Manufacturer
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms\Data\Collection;
use Sfcms\Model;
use Module\Market\Form\ManufacturerEditForm;

class ManufacturerModel extends Model
{

    /**
     * Вернет коллекцию производителей, подходящую под список id категорий
     * @param array $catIds
     * @return Collection|null
     */
    public function findAllByCatalogCategories( array $catIds )
    {
        if ( 0 == count($catIds) ) {
            return $this->createCollection();
        }
        $catalogTable   = $this->getModel('Catalog')->getTable();
        $manufTable     = $this->getTable();
        $sql = sprintf('SELECT m.* FROM `%s` m '
                . 'INNER JOIN `%s` c ON '
                    . 'c.manufacturer = m.id AND c.deleted = 0 AND c.hidden = 0 AND c.cat = 0 AND c.parent IN ('.join(',',$catIds).')'
                . ' GROUP BY m.id', $manufTable, $catalogTable );
        $manufList  = $this->getDB()->fetchAll( $sql );
        $collection = $this->createCollection( $manufList );
        return $collection;
    }


    /**
     * @return ManufacturerEditForm
     */
    public function getForm()
    {
        return new ManufacturerEditForm();
    }
}
