<?php
/**
 * Таблица настроек модулей сайта
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_Settings extends Data_Table
{
    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            //new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Varchar('module', 100),
            new Data_Field_Varchar('property', 100),
            new Data_Field_Varchar('value', 100),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'settings';
    }

    protected function getPk()
    {
        return 'module,property';
    }
}
