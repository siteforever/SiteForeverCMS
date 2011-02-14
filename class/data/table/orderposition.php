<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Data_Table_OrderPosition extends Data_Table
{

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            $this->getInt('id', 11, true, null, true),
            $this->getInt('ord_id'),
            $this->getVarchar('articul', 250),
            $this->getText('details'),
            $this->getVarchar('currency', 10),
            $this->getVarchar('item', 10),
            $this->getInt('cat_id'),
            $this->getDecimal('price'),
            $this->getInt('count'),
            $this->getInt('status'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'order_pos';
    }

    protected function getKeys()
    {
        return array(
            'ord_id',
            'cat_id',
            'articul',
        );
    }
}
