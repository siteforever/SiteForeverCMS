<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Table_User extends Data_Table
{
    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'users';
    }

    /**
     * Вернет список полей
     * @return array
     */
    public function getFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Varchar('login', 50),
            new Data_Field_Varchar('password', 40),
            new Data_Field_Varchar('solt', 8),
            new Data_Field_Varchar('fname', 20),
            new Data_Field_Varchar('lname', 20),
            new Data_Field_Varchar('email', 50),
            new Data_Field_Varchar('name', 250),
            new Data_Field_Varchar('phone', 20),
            new Data_Field_Varchar('fax', 20),
            new Data_Field_Varchar('inn', 20),
            new Data_Field_Varchar('kpp', 20),
            new Data_Field_Text('address'),
            new Data_Field_Tinyint('status', 4, true, '0'),
            new Data_Field_Int('date'),
            new Data_Field_Int('last'),
            new Data_Field_Int('perm'),
            new Data_Field_Varchar('confirm', 32),
            new Data_Field_Text('basket'),
        );
    }
}
