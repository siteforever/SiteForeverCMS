<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Data_Table_OrderStatus extends Data_Table
{

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            $this->getInt('id', 11, true, null, true),
            $this->getInt('status'),
            $this->getVarchar('name', 100),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'order_status';
    }
}
