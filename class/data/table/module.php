<?php
/**
 * DB Table Module
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Module extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Varchar( 'name', 250, false, null, false ),
            new Data_Field_Varchar( 'path', 250, false, null, false ),
            new Data_Field_Blob( 'config', 11, false, null, false ),
            new Data_Field_Text( 'desc', 11, false, null, false ),
            new Data_Field_Int( 'pos', 11, false, null, false ),
            new Data_Field_Int( 'active', 11, false, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'module';
    }
}