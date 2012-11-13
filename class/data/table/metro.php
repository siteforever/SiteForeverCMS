<?php
/**
 * DB Table Metro
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Metro extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 10, false, null, true ),
            new Data_Field_Varchar( 'name', 50, true, null, false ),
            new Data_Field_Int( 'city_id', 10, false, null, false ),
            new Data_Field_Decimal( 'lat', 10, true, null, false ),
            new Data_Field_Decimal( 'lng', 10, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'metro';
    }
}