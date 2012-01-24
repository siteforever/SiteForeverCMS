<?php
/**
 * Тест фильтров для каталога
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Sfcms_Catalog_FilterTest extends PHPUnit_Framework_TestCase
{
    protected $filter;


    /*protected function _setUp()
    {
        $filter = new Sfcms_Catalog_Filter();

        $filter->addFilterGroup( $boiler = new Sfcms_Catalog_Filter_Group(1) );

        $boiler->addFilterGroup( $boiler13 = new Sfcms_Catalog_Filter_Group(13) );
        $boiler13->addFilterItem( 0, new Sfcms_Catalog_Filter_Item_Select('Производитель',
            array(
                 'Buderus', 'Viessmann', 'Baxi', 'Wolf', 'Vaillant'
            )) );

        $boiler->addFilterGroup( $boiler14 = new Sfcms_Catalog_Filter_Group(14) );
        $boiler14->addFilterItem( 0, new Sfcms_Catalog_Filter_Item_Select('Производитель',
            array(
                 'Buderus', 'Viessmann', 'Baxi', 'Wolf', 'Vaillant'
            )) );

        $boiler->addFilterGroup( $boiler15 = new Sfcms_Catalog_Filter_Group(14) );
        $boiler15->addFilterItem( 0, new Sfcms_Catalog_Filter_Item_Select('Производитель',
            array(
                 'Protherm', 'Kospel', 'Wespe Heizung', 'Rusnit',
            )) );
    }*/

    public function testGetFilterValues()
    {/*
        $this->markTestSkipped();
        $this->assertEquals(
            array('Buderus', 'Viessmann', 'Baxi', 'Wolf', 'Vaillant','Protherm', 'Kospel', 'Wespe Heizung', 'Rusnit'),
            $this->filter->getValues()
        );
    */}
}
