<?php
/**
 * Интерфейс корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
abstract class Basket
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var Data_Object_User
     */
    protected $user = null;

    public function __construct( Data_Object $user )
    {
        App::getInstance()->addScript('/misc/etc/basket.js');

        $this->user = $user;
        $this->data = array();

        $this->load();
    }

    /**
     * Добавить товар в корзину
     * @param string $id
     * @param string $name
     * @param int $count
     * @param float $price
     * @param string $details
     *
     * @return boolean
     */
    public function add( $id = '', $name = '', $count = 0, $price = 0.0, $details = '' )
    {
        if ( ! is_array( $this->data ) ) {
            $this->data = array();
        }

        if ( ! $id )
            $id = $name;

        if ( $id && ! $name ) {
            $name   = $id;
        }

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
        return true;
    }

    /**
     * Установить новое значение товара
     * @param  $id
     * @param  $count
     * @return boolean
     */
    public function setCount( $name, $count )
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
    public function getCount( $name = '' )
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


    /**
     * @param $name
     * @return int
     */
    public function getPrice( $name )
    {
        foreach ( $this->data as $prod ) {
            if ( @$prod['name'] == $name || @$prod['id'] == $name ) {
                return $prod['price'];
            }
        }
        return 0;
    }


    /**
     * Удалить из корзины указанное количество тавара
     * @param $name
     * @param int $count
     * @return int|null
     */
    public function del( $name, $count = 0 )
    {
        $old_count  = $this->getCount( $name );
        $new_count  = $old_count - $count;

        if ( $count <= 0 || $new_count <= 0 ) {
            $this->setCount($name, 0);
            return 0;
        }
        $this->setCount($name, $new_count);
        return $new_count;
    }

    /**
     * Вся информация о товарах в корзине
     * @return array
     */
    public function getAll()
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
    public function count()
    {
        return count($this->data);
    }
    
    /**
     * Сумма заказа
     * @param $name
     * @return float
     */
    public function getSum( $name = '' )
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
    public function clear()
    {
        $this->data = array();
    }

    /**
     * Сохранить
     */
    abstract public function save();
    
    /**
     * Загрузить
     */
    abstract public function load();
}