<?php
/**
 * Описание данных структуры
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Data_Table_Page extends Data_Table
{
    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'structure';
    }

    /**
     * Вернет список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Int('parent', 11, true, '0'),
            new Data_Field_Varchar('name', 80, true, ''),
            new Data_Field_Varchar('template', 50, true, 'inner'),
            new Data_Field_Varchar('alias', 250, true, ''),
//            new Data_Field_Int('alias_id', 11, true, '0'),
            new Data_Field_Text('path'),
            new Data_Field_Int('date', 11, true, '0'),
            new Data_Field_Int('update', 11, true, '0'),
            new Data_Field_Int('pos', 11, true, '0'),
            new Data_Field_Int('link', 11, true, '0'),
            new Data_Field_Varchar('controller', 20, true, 'page'),
            new Data_Field_Varchar('action', 20, true, 'index'),
            new Data_Field_Varchar('sort', 20, true, 'pos ASC'),
            new Data_Field_Varchar('title', 80, true, ''),
            new Data_Field_Text('notice'),
            new Data_Field_Text('content'),
            new Data_Field_Varchar('thumb', 250, true, ''),
            new Data_Field_Varchar('image', 250, true, ''),
            new Data_Field_Varchar('keywords', 120, true, ''),
            new Data_Field_Varchar('description', 120, true, ''),
            new Data_Field_Int('author', 11, true, '0'),
            new Data_Field_Tinyint('hidden', 4, true, '0'),
            new Data_Field_Tinyint('protected', 4, true, '0'),
            new Data_Field_Tinyint('system', 4, true, '0'),
            new Data_Field_Tinyint('deleted', 1, true, '0'),
        );
    }

    protected function getKeys()
    {
        return array(
            'id_structure'  => 'parent',
            //'url'           => 'uri',
            'date'          => 'date',
            'order'         => array('parent','pos'),
            'request'       => 'alias'
        );
    }
}