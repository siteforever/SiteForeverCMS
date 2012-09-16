<?php
/**
 * DB Table Product_Property
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Product_Property extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'product_id', 11, false, null, false ),
            new Data_Field_Int( 'product_field_id', 11, false, null, false ),
            new Data_Field_Varchar( 'value_string', 255, true, null, false ),
            new Data_Field_Blob( 'value_text', 11, true, null, false ),
            new Data_Field_Int( 'value_int', 11, true, null, false ),
            new Data_Field_Datetime( 'value_datetime', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'product_property';
    }
}