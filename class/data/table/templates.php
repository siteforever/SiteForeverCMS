<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Data_Table_Templates extends Data_Table
{

    /**
     * Вернет список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Varchar('name', 100, true),
            new Data_Field_Varchar('description', 250),
            new Data_Field_Text('template'),
            new Data_Field_Int('update'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'templates';
    }

    protected function getPk()
    {
        return 'name';
    }
}
