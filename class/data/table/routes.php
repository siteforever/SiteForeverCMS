<?php
/**
 * Таюлица маршрутизации
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_Routes extends Data_Table
{

    /**
     * Вернет список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, false, null, true),
            new Data_Field_Int('pos'),
            new Data_Field_Varchar('alias', 200),
            new Data_Field_Varchar('controller', 50, true, 'page'),
            new Data_Field_Varchar('action', 50, true, 'index'),
            new Data_Field_Tinyint('active'),
            new Data_Field_Tinyint('protected'),
            new Data_Field_Tinyint('system'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'routes';
    }
}