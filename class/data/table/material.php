<?php
/**
 * DB Table Material
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Material extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Varchar( 'name', 255, true, null, false ),
            new Data_Field_Varchar( 'image', 255, true, null, false ),
            new Data_Field_Int( 'active', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'material';
    }
}