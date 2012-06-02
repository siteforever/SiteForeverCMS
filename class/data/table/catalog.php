<?php
/**
 * Таблица каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_Catalog extends Data_Table
{

    /**
     * Вернет список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Int('parent'),
            new Data_Field_Tinyint('cat'),
            new Data_Field_Varchar('name', 100),
            new Data_Field_Varchar('url', 100),
            new Data_Field_Text('path'),
            new Data_Field_Varchar('icon', 250),
            new Data_Field_Text('text'),
            new Data_Field_Varchar('articul', 250),
            new Data_Field_Decimal('price1'),
            new Data_Field_Decimal('price2'),
            new Data_Field_Int('manufacturer'),
            new Data_Field_Int('pos'),
            new Data_Field_Varchar('p0', 250),
            new Data_Field_Varchar('p1', 250),
            new Data_Field_Varchar('p2', 250),
            new Data_Field_Varchar('p3', 250),
            new Data_Field_Varchar('p4', 250),
            new Data_Field_Varchar('p5', 250),
            new Data_Field_Varchar('p6', 250),
            new Data_Field_Varchar('p7', 250),
            new Data_Field_Varchar('p8', 250),
            new Data_Field_Varchar('p9', 250),
            new Data_Field_Tinyint('sort_view', 1, true, '1'),
            new Data_Field_Tinyint('top', 1, true, '0'),
            new Data_Field_Tinyint('byorder', 1, true, '0'),
            new Data_Field_Tinyint('absent', 1, true, '0'),
            new Data_Field_Tinyint('hidden', 1, true, '0'),
            new Data_Field_Tinyint('protected', 1, true, '0'),
            new Data_Field_Tinyint('deleted', 1, true, '0'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'catalog';
    }
}
