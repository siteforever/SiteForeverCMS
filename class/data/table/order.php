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
            // field, size, nonull, default, autoincrement
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Tinyint('status', 4, true, 0),
            new Data_Field_Tinyint('paid', 1, true, 0),
            new Data_Field_Int('delivery_id', 11, true, 0),
            new Data_Field_Int('payment_id', 11, true, 0),
            new Data_Field_Int('date', 11, true, 0),
            new Data_Field_Int('user_id', 11, true, 0),
            new Data_Field_Varchar('fname', 255, true, ""),
            new Data_Field_Varchar('lname', 255, true, ""),
            new Data_Field_Varchar('email', 255, true, ""),
            new Data_Field_Varchar('phone', 255, true, ""),
            new Data_Field_Text('address'),
            new Data_Field_Text('comment'),
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
