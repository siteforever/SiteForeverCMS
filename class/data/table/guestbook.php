<?php
/**
 * DB Table Guestbook
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_Guestbook extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int( 'id', 11, false, null, true ),
            new Data_Field_Int( 'link', 11, true, null, false ),
            new Data_Field_Varchar( 'name', 250, true, null, false ),
            new Data_Field_Varchar( 'email', 250, true, null, false ),
            new Data_Field_Varchar( 'site', 250, true, null, false ),
            new Data_Field_Varchar( 'city', 250, true, null, false ),
            new Data_Field_Int( 'date', 11, true, null, false ),
            new Data_Field_Varchar( 'ip', 15, true, null, false ),
            new Data_Field_Text( 'message', 11, true, null, false ),
            new Data_Field_Text( 'answer', 11, true, null, false ),
            new Data_Field_Tinyint( 'hidden', 1, false, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return 'guestbook';
    }
}