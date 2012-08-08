<?php
/**
 * Позиция в заказе
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @property $summa
 * @property $count
 * @property $price
 * @property $articul
 */
class Data_Object_OrderPosition extends Data_Object
{
    public function getSumma()
    {
        return $this->count * $this->price;
    }
}
