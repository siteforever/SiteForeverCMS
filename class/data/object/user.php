<?php
/**
 * Объект пользователя
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Data_Object_User extends Data_Object
{
    /**
     * Поменять пароль пользователя
     * @param string $password
     * @return void
     */
    function changePassword( $password )
    {
        $solt = App::getInstance()->getAuth()->generateString(8);
        $hash = App::getInstance()->getAuth()->generatePasswordHash( $password, $solt );

        $this->offsetSet( 'solt', $solt );
        $this->offsetSet( 'password', $hash );
        return $this;
    }

    /**
     * Активировать пользователя
     * @return void
     */
    function active()
    {
        $this->data['status']   = 1;
    }

    /**
     * Деактивировать пользователя
     * @return void
     */
    function deactive()
    {
        $this->data['status']   = 0;
    }

}
