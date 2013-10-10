<?php
/**
 * Заказ
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\Market\Object;

use Sfcms;
use Sfcms\Data\Object;
use Sfcms\Data\Field;
use Sfcms\Data\Collection;
use Module\User\Object\User;
use Module\Market\Object\OrderStatus;
use Module\Market\Object\Payment;
use Module\Market\Object\Delivery;

/**
 * @property $id
 * @property $status
 * @property $paid
 * @property $delivery
 * @property $date
 * @property $user_id
 * @property $fname
 * @property $lname
 * @property $email
 * @property $phone
 * @property $address
 * @property $comment
 *
 * @property int $Count
 * @property OrderStatus $Status
 * @property User $User
 * @property Payment $Payment
 * @property Delivery $Delivery
 * @property Collection $Positions
 */
class Order extends Object
{

    /**
     * Вернет хэш для заказа
     * @param null $id
     * @param null $date
     * @param null $email
     * @return string
     */
    public function getHash($id = null, $date = null, $email = null)
    {
        if (null === $id) {
            if (null === $this->id) {
                throw new \InvalidArgumentException('Order not defined');
            }
            $id    = $this->id;
            $date  = $this->date;
            $email = $this->email;
        }

        return md5($id . ':' . $date . ':' . $email);
    }

    /**
     * Проверит хэш на соответствие объекту
     * @param $hash
     * @return bool
     */
    public function validateHash( $hash )
    {
        return $this->getHash() == $hash;
    }

    /**
     * Вернет адрес открытия заказа
     * @return string
     */
    public function getUrl()
    {
        return Sfcms::html()->url('order/view', array('id'=>$this->getId(), 'code'=>$this->getHash()));
    }

    /**
     * Имя покупателя
     * @return string
     */
    public function getEmptorName()
    {
        return trim($this->fname . ' ' . $this->lname);
    }

    public function getSum()
    {
        return $this->Positions ? $this->Positions->sum('sum') : 0;
    }

    public function getRobokassa(Payment $payment, Sfcms\DeliveryManager $delivery, Sfcms\Config $config)
    {
        $robokassa = null;
        switch ($payment->module) {
            case 'robokassa' :
                $robokassa = new Sfcms\Robokassa($config->get('service.robokassa'));
                $robokassa->setInvId($this->id);
                $robokassa->setOutSum($this->getSum() + $delivery->cost($this->getSum()));
                $robokassa->setDesc(
                    sprintf(
                        'Оплата заказа №%s в интернет-магазине %s',
                        $this->id,
                        $config->get('sitename')
                    )
                );
                break;
            case 'basket':
            default:
        }
        return $robokassa;
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            // field, size, nonull, default, autoincrement
            new Field\Int('id', 11, true, null, true),
            new Field\Tinyint('status', 4, true, 0),
            new Field\Tinyint('person', 4, false),
            new Field\Int('paid', 11, true, 0),
            new Field\Int('delivery_id', 11, true, 0),
            new Field\Int('payment_id', 11, true, 0),
            new Field\Int('date', 11, true, 0),
            new Field\Int('user_id', 11, true, 0),
            new Field\Varchar('fname', 255, true, ""),
            new Field\Varchar('lname', 255, true, ""),
            new Field\Varchar('email', 255, true, ""),
            new Field\Varchar('phone', 255, true, ""),
            new Field\Text('address'),
            new Field\Text('details'),
            new Field\Text('passport'),
            new Field\Text('comment'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'order';
    }

    public static function keys()
    {
        return array('status'=>'status', 'user_id'=>array('date','user_id'), 'date'=>'date');
    }
}
