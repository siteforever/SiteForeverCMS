<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Data_Table_Test extends Data_Table
{

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            $this->getInt('id',11,true,null,true),
            $this->getVarchar('value'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'test';
    }
}
