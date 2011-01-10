<?php
/**
 * Таблица новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_News extends Data_Table
{

    /**
     * Вернет список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Int('cat_id'),
            new Data_Field_Int('author_id'),
            new Data_Field_Varchar('name', 250),
            new Data_Field_Text('notice'),
            new Data_Field_Text('text'),
            new Data_Field_Int('date'),
            new Data_Field_Varchar('title', 250),
            new Data_Field_Varchar('keywords', 250),
            new Data_Field_Varchar('description', 250),
            new Data_Field_Tinyint('hidden'),
            new Data_Field_Tinyint('protected'),
            new Data_Field_Tinyint('deleted'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'news';
    }
}
