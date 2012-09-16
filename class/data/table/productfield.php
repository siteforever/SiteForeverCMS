<?php
/**
 * DB Table Product_Field
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Product_Field extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Int( 'product_type_id', 11, false, null, false ),
            new Data_Field_Varchar( 'type', 250, false, null, false ),
            new Data_Field_Varchar( 'name', 250, false, null, false ),
            new Data_Field_Varchar( 'unit', 250, false, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'product_field';
    }
}