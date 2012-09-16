<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_NewsCategory extends Data_Table
{

    /**
     * Вернет список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Varchar('name', 250),
            new Data_Field_Text('description'),
            new Data_Field_Tinyint('show_content', 1),
            new Data_Field_Tinyint('show_list', 1),
            new Data_Field_Tinyint('type_list', 1),
            new Data_Field_Tinyint('per_page', 1),
            new Data_Field_Tinyint('hidden', 1),
            new Data_Field_Tinyint('protected', 1),
            new Data_Field_Tinyint('deleted', 1, true, 0),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'news_cats';
    }
}