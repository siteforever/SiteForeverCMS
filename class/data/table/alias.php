<?php
/**
 * Таблица Алиасов
 * @author keltanas
 */
 
class Data_Table_Alias extends Data_Table
{

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'aliases';
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Varchar('alias', 255),
            new Data_Field_Varchar('url', 255),
//            new Data_Field_Varchar('controller', 255),
//            new Data_Field_Varchar('action', 255),
//            new Data_Field_Text('params'),
        );
    }
}
