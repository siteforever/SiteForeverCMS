<?php
/**
 * DB Table Delivery
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Delivery extends Data_Table
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
            new Data_Field_Text( 'desc', 11, true, null, false ),
            new Data_Field_Decimal( 'cost', 13, true, null, false ),
            new Data_Field_Tinyint( 'active', 1, true, null, false ),
            new Data_Field_Int( 'pos', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'delivery';
    }
}