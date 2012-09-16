<?php
/**
 * DB Table Payment
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Payment extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Varchar( 'name', 255, false, null, false ),
            new Data_Field_Text( 'desc', 11, false, null, false ),
            new Data_Field_Varchar( 'module', 255, false, null, false ),
            new Data_Field_Tinyint( 'active', 1, false, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'payment';
    }
}