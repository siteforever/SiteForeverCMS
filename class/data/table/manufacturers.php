<?php
/**
 * DB Table Manufacturers
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Manufacturers extends Data_Table
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
            new Data_Field_Varchar( 'phone', 250, true, null, false ),
            new Data_Field_Varchar( 'email', 250, true, null, false ),
            new Data_Field_Varchar( 'site', 250, true, null, false ),
            new Data_Field_Text( 'address', 11, true, null, false ),
            new Data_Field_Varchar( 'image', 250, true, null, false ),
            new Data_Field_Text( 'description', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'manufacturers';
    }
}