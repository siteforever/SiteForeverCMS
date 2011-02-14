<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Data_Table_Order extends Data_Table
{

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Tinyint('status'),
            new Data_Field_Int('date'),
            new Data_Field_Int('user_id'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'order';
    }

    protected function getKeys()
    {
        return array('status'=>'status', 'user_id'=>array('date','user_id'), 'date'=>'date');
    }
}
