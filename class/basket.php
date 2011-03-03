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
        //$request->addScript('/misc/etc/jquery.blockUI.js');

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
    function add( $id = '', $name, $count, $price, $details = '' )
    {
        if ( ! is_array( $this->data ) ) {
            throw new Basket_Exception('Basket data corrupted');
        }

        if ( ! $id )
            $id = $name;

        if ( $id && ! $name )
            $name   = $id;

        foreach ( $this->data as &$prod ) {
            if ( @$prod['name'] == $name || @$prod['id'] == $id ) {
                $prod['count'] += $count;
                $prod['price']  = $price;
                $prod['details']    = $details;
                return true;
            }
        }
        $this->data[] = array(
            'id'    => $id,
            'name'  => $name,
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
    function setCount( $name, $count )
    {
        foreach ( $this->data as $i => &$prod ) {
            if ( @$prod['name'] == $name || @$prod['id'] == $name ) {
                if ( $count > 0 )
                    $prod['count']  = $count;
                else
                    unset( $this->data[$i] );
                
                return true;
            }
        }
        return false;
    }

    /**
     * Количество данного товара в корзине
     * @param string $id
     */
    function getCount( $name = '' )
    {
        if ( ! is_array( $this->data ) ) {
            throw new Basket_Exception('Basket data corrupted');
        }
        if ( $name ) {
            foreach ( $this->data as $prod ) {
                if ( @$prod['name'] == $name || @$prod['id'] == $name ) {
                    return $prod['count'];
                }
            }
            return null;
        }
        else {
            $count = 0;
            foreach( $this->data as $prod ) {
                $count += $prod['count'];
            }
            return $count;
        }
    }
    
    function getPrice( $name )
    {
        foreach ( $this->data as $prod ) {
            if ( @$prod['name'] == $name || @$prod['id'] == $name ) {
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
    function del( $name, $count = 0 )
    {
        $old_count  = $this->getCount( $name );
        $new_count  = $old_count - $count;

        if ( $count <= 0 || $new_count <= 0 ) {
            $this->setCount($name, 0);
            return 0;
        }
        $this->setCount($name, $new_count);
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
    function getSum( $name = '' )
    {
        if ( ! is_array( $this->data ) ) {
            throw new Basket_Exception('Basket data corrupted');
        }
        if ( $name ) {
            foreach ( $this->data as $prod ) {
                if ( @$prod['name'] == $name || @$prod['id'] == $name ) {
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