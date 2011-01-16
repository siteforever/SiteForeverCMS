<?php
/**
 * Талблица изображений галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_Gallery extends Data_Table
{
    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id',11,true,null,true),
            new Data_Field_Int('category_id'),
            new Data_Field_Varchar('name', 250),
            new Data_Field_Varchar('link', 250),
            new Data_Field_Text('description'),
            new Data_Field_Varchar('image', 250),
            new Data_Field_Varchar('middle', 250),
            new Data_Field_Varchar('thumb', 250),
            new Data_Field_Int('pos'),
            new Data_Field_Tinyint('main'),
            new Data_Field_Tinyint('hidden'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'gallery';
    }
}
