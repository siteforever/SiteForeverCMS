<?php
/**
 * Интерфейс корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
abstract class Basket
{
    protected $data = array();
    protected $user;

    function __construct( Data_Object $user )
    {
        $this->user = $user;
        $this->data = array();
        $this->load();
    }
    
    /**
     * Добавить товар в корзину
     * @param string $id
     * @param int $count
     * @param float $price
     * @param string $details
     */
    function add( $id, $count, $price, $details = '' )
    {
        $this->data[] = array(
            'id'    => $id,
            'count' => $count,
            'price' => $price,
            'details'=>$details,
        );
    }

    /**
     * Установить новое значение товара
     * @param  $id
     * @param  $count
     * @return void
     */
    function setCount( $id, $count )
    {
        if ( isset( $this->data[$id] ) ) {
            $this->data[$id]['count']   = $count;
            return true;
        }
        return false;
    }

    /**
     * Количество данного товара в корзине
     * @param string $id
     */
    function getCount( $id = 0 )
    {
        if ( $id ) {
            if ( isset( $this->data[ $id ] ) ) {
                return $this->data[ $id ]['count'];
            }
        }
        else {
            $count = 0;
            if ( is_array($this->data) ) {
                foreach( $this->data as $item ) {
                    $count += $item['count'];
                }
                return $count;
            }
        }
    }
    
    function getPrice( $id )
    {
        if ( isset( $this->data[ $id ] ) ) {
            return $this->data[ $id ]['price'];
        }
    }
    
    /**

    {* Удалить из корзины указанное количество тавара
     * @param string $id
     * @param int $count
     */
    function del( $id, $count = 0 )
    {
        if ( isset( $this->data[ $id ] ) ) {

            if ( $count == 0 || $this->data[ $id ]['count'] <= $count ) {
                unset( $this->data[ $id ] );
            }
            else {
                $this->data[ $id ]['count'] -= $count;
            }
        }
    }

    /**
     * Вся информация о товарах в корзине
     */
    function getAll()
    {
        if ( ! $this->data ) {
            $this->data = array();
        }
        return $this->data;
    }
    
    /**
     * Количество позиций
     * @return int
     */
    function count()
    {
        return count($this->data);
    }
    
    /**
     * Сумма заказа
     * @return float
     */
    function getSum()
    {
        $summa = 0;
        if ( is_array($this->data) ) {
            foreach( $this->data as $prod ) {
                $summa += $prod['count'] * $prod['price'];
            }
        }
        return $summa;
    }

    /**
     * Очистить корзину
     * @return void
     */
    function clear()
    {
        $this->data = array();
        $this->save();
    }

    /**
     * Сохранить
     */
    abstract function save();
    
    /**
     * Загрузить
     */
    abstract function load();
}