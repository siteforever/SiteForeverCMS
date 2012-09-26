<?php
/**
 * Объект пользователя
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 *
 * @property $id
 * @property $name
 * @property $fname
 * @property $lname
 * @property $email
 * @property $phone
 * @property $basket
 * @property $address
 */
class Data_Object_User extends Data_Object
{
    /**
     * Поменять пароль пользователя
     * @param string $password
     * @return Data_Object_User
     */
    public function changePassword( $password )
    {
        $solt = App::getInstance()->getAuth()->generateString( 8 );
        $hash = App::getInstance()->getAuth()->generatePasswordHash( $password, $solt );
        $this->set('solt', $solt);
        $this->set('password', $hash);
        return $this;
    }

    /**
     * Активировать пользователя
     * @return void
     */
    public function active()
    {
        $this->set('status', 1);
    }

    /**
     * Деактивировать пользователя
     * @return void
     */
    public function deactive()
    {
        $this->set('status', 0);
    }


    /**
     * Возвращает права пользователя
     * @return int
     */
    public function getPermission()
    {
        return $this->data['perm'];
    }

    /**
     * Проверит наличие прав у пользователя
     * @param int $perm
     * @return bool
     */
    public function hasPermission( $perm )
    {
        if ( $this->data['perm'] >= $perm ) {
            return 1;
        }
        return 0;
    }

    /**
     * Проверит, равны ли права значению
     * @param int $perm
     * @return bool
     */
    public function eqPermission( $perm )
    {
        return $this->data['perm'] == $perm ? true : false;
    }


}
