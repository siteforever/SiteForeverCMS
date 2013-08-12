<?php
namespace Sfcms\Basket;

use App;
use Sfcms\Data\Object;
use Module\User\Object\User;
use Sfcms\Request;
use Sfcms_Basket_Exception;

/**
 * Интерфейс корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
abstract class Base
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var User
     */
    protected $user;

    /** @var Request */
    protected $request;

    public function __construct(Request $request, Object $user = null)
    {
        App::cms()->addScript('/misc/etc/basket.js');

        $this->request = $request;
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
    public function add($id = '', $name = '', $count = 0, $price = 0.0, $details = '')
    {
        if (!is_array($this->data)) {
            $this->data = array();
        }

        if (null === $id) {
            $id = $name;
        }

        foreach ($this->data as &$prod) {
            if ((@$prod['name'] == $name || @$prod['id'] == $id) && $prod['details'] == $details) {
                $prod['count'] += $count;
                $prod['price'] = $price;
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

        return count($this->data) - 1;
    }

    /**
     * Установить новое значение товара
     * @param  $id
     * @param  $count
     * @return boolean
     */
    public function setCount($id, $count)
    {
        foreach ($this->data as $i => &$prod) {
            if (@$prod['name'] == $id || @$prod['id'] == $id) {
                if ($count > 0) {
                    $prod['count'] = $count;
                } else {
                    unset($this->data[$i]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Количество данного товара в корзине
     *
     * @param string|int $id
     *
     * @return int
     * @throws Sfcms_Basket_Exception
     */
    public function getCount($id = null)
    {
        if (!is_array($this->data)) {
            throw new Sfcms_Basket_Exception('Basket data corrupted');
        }
        $result = 0;
        if ($id) {
            foreach ($this->data as $prod) {
                if (@$prod['name'] == $id || @$prod['id'] == $id) {
                    $result += $prod['count'];
                }
            }

            return $result;
        }
        foreach ($this->data as $prod) {
            $result += $prod['count'];
        }

        return $result;
    }

    /**
     * @param $key
     *
     * @return null|array
     */
    public function getByKey($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
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
     * @param int $key
     * @param int $count
     * @return int|null
     */
    public function del($key, $count = 0)
    {
        if (!is_numeric($key) && !is_int($key)) {
            throw new Sfcms_Basket_Exception('For delete need usage integer handler into basket');
        }
        if ($count < 0) {
            throw new Sfcms_Basket_Exception('');
        }
        if (!isset($this->data[$key])) {
            return false;
        }
        $old_count = $this->data[$key]['count'];
        $new_count = $old_count - $count;

        if ($count == 0 || $new_count <= 0) {
//            $this->data[$key]['count'] = 0;
            unset($this->data[$key]);

            return 0;
        }
        $this->data[$key]['count'] = $new_count;

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
     *
     * @param $id
     *
     * @return float
     * throws Sfcms_Basket_Exception
     */
    public function getSum($id = null)
    {
        if (!is_array($this->data)) {
            throw new Sfcms_Basket_Exception('Basket data corrupted');
        }
        $result = 0.0;
        if (null !== $id) {
            foreach ($this->data as $prod) {
                if (@$prod['name'] == $id || @$prod['id'] == $id) {
                    $result += $prod['count'] * @$prod['price'];
                }
            }

            return $result;
        }
        foreach ($this->data as $prod) {
            $result += $prod['count'] * @$prod['price'];
        }

        return $result;
    }

    public function getKeys()
    {
        $result = array();
        foreach( $this->data as $prod ) {
            if ( isset( $prod['id'] ) && is_numeric( $prod['id'] ) ) {
                $result[] = $prod['id'];
            }
        }
        if ( count( $result ) ) {
            return $result;
        }
        return null;
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
