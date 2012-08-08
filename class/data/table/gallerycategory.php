<?php
/**
 * DB Table Gallery_Category
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_GalleryCategory extends Data_Table
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
            new Data_Field_Tinyint( 'middle_method', 4, true, null, false ),
            new Data_Field_Int( 'middle_width', 11, true, null, false ),
            new Data_Field_Int( 'middle_height', 11, true, null, false ),
            new Data_Field_Tinyint( 'thumb_method', 4, true, null, false ),
            new Data_Field_Int( 'thumb_width', 11, true, null, false ),
            new Data_Field_Int( 'thumb_height', 11, true, null, false ),
            new Data_Field_Varchar('target', 10),
            new Data_Field_Varchar('thumb', 250),
            new Data_Field_Int( 'perpage', 11, true, null, false ),
            new Data_Field_Varchar( 'color', 20, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'gallery_category';
    }
}