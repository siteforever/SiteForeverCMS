<?php

class Basket_Exception extends Exception {};

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
        $request    = App::getInstance()->getRequest();
        $request->addScript('/misc/etc/basket.js');

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
        if ( ! is_array( $this->data ) ) {
            throw new Basket_Exception('Basket data corrupted');
        }
        foreach ( $this->data as &$prod ) {
            if ( $prod['id'] == $id ) {
                $prod['count'] += $count;
                $prod['price']  = $price;
                $prod['details']    = $details;
                return true;
            }
        }
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
        foreach ( $this->data as &$prod ) {
            if ( $prod['id'] == $id ) {
                $prod['count']  = $count;
                return true;
            }
        }
        return false;
    }

    /**
     * Количество данного товара в корзине
     * @param string $id
     */
    function getCount( $id = '' )
    {
        if ( ! is_array( $this->data ) ) {
            throw new Basket_Exception('Basket data corrupted');
        }
        if ( $id ) {
            foreach ( $this->data as $prod ) {
                if ( $prod['id'] == $id ) {
                    return $prod['count'];
                }
            }
        }
        else {
            $count = 0;
            foreach( $this->data as $prod ) {
                $count += $prod['count'];
            }
            return $count;
        }
    }
    
    function getPrice( $id )
    {
        foreach ( $this->data as $prod ) {
            if ( $prod['id'] == $id ) {
                return $prod['price'];
            }
        }
        return 0;
    }
    
    /**

    {* Удалить из корзины указанное количество тавара
     * @param string $id
     * @param int $count
     */
    function del( $id, $count = 0 )
    {
        foreach ( $this->data as $i => $prod ) {
            if ( $prod['id'] == $id ) {
                $new_count  = $prod['count'] - $count;
                if ( $new_count < 0 ) {
                    unset( $this->data[$i] );
                    break;
                }
                $this->data[$i]['count']    = $new_count;
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
    function getSum( $id = '' )
    {
        if ( ! is_array( $this->data ) ) {
            throw new Basket_Exception('Basket data corrupted');
        }
        if ( $id ) {
            foreach ( $this->data as $prod ) {
                if ( $prod['id'] == $id ) {
                    return $prod['count'] * $prod['price'];
                }
            }
        }
        else {
            $summa = 0;
            foreach( $this->data as $prod ) {
                $summa += $prod['count'] * $prod['price'];
            }
            return $summa;
        }
    }

    /**
     * Очистить корзину
     * @return void
     */
    function clear()
    {
        $this->data = array();
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