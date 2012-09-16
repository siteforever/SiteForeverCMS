<?php
/**
 * DB Table Gallery
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Gallery extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Int( 'category_id', 11, true, null, false ),
            new Data_Field_Varchar( 'alias', 250, true, null, false ),
            new Data_Field_Varchar( 'name', 250, true, null, false ),
            new Data_Field_Varchar( 'link', 250, true, null, false ),
            new Data_Field_Text( 'description', 11, true, null, false ),
            new Data_Field_Varchar( 'image', 250, true, null, false ),
            new Data_Field_Int( 'pos', 11, true, null, false ),
            new Data_Field_Tinyint( 'main', 1, true, 0 ),
            new Data_Field_Tinyint( 'hidden', 1, true, 0 ),
            new Data_Field_Tinyint( 'deleted', 1, true, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'gallery';
    }
}