<?php
/**
 * DB Table Product_Type
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_ProductType extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Varchar( 'name', 250, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'product_type';
    }
}