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
}
