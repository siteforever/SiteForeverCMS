<?php
/**
 * Таблица галереи каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_CatGallery extends Data_Table
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
            new Data_Field_Varchar('title', 250),
            new Data_Field_Varchar('descr', 250),
            new Data_Field_Varchar('image', 250),
            new Data_Field_Varchar('middle', 250),
            new Data_Field_Varchar('thumb', 250),
            new Data_Field_Tinyint('hidden'),
            new Data_Field_Tinyint('main'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'catalog_gallery';
    }
}
