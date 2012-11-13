<?php
/**
 * Заказ
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

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
 * @property Data_Object_OrderStatus $Status
 * @property Data_Object_User $User
 * @property Data_Object_Payment $Payment
 * @property Data_Object_Delivery $Delivery
 * @property Data_Collection $Positions
 */
class Data_Object_Order extends Data_Object
{

    /**
     * Вернет хэш для заказа
     * @param null $id
     * @param null $date
     * @param null $email
     * @return string
     */
    private function getHash( $id = null, $date = null, $email = null )
    {
        if ( null === $id ) {
            if ( null === $this->id ) throw new InvalidArgumentException('Order not defined');
            $id     = $this->id;
            $date   = $this->date;
            $email  = $this->email;
        }
        return md5( $id.':'.$date.':'.$email );
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
        return /*'http://'.$this->app()->getConfig('siteurl')*/
            Sfcms::html()->url('order/view',array('id'=>$this->getId(),'code'=>$this->getHash()));
    }

    /**
     * Имя покупателя
     * @return string
     */
    public function getEmptorName()
    {
        return trim( $this->fname . ' ' . $this->lname );
    }
}
