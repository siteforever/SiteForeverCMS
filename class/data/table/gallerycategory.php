<?php
/**
 * Таблица категорий галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_GalleryCategory extends Data_Table
{

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id',11,true,null,true),
            new Data_Field_Varchar('name', 250),
            new Data_Field_Tinyint('middle_method'),
            new Data_Field_Int('middle_width'),
            new Data_Field_Int('middle_height'),
            new Data_Field_Tinyint('thumb_method'),
            new Data_Field_Int('thumb_width'),
            new Data_Field_Int('thumb_height'),
            new Data_Field_Varchar('color', 20),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'gallery_category';
    }
}
