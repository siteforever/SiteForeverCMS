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
        $solt = App::getInstance()->getAuth()->generateString( 8 );
        $hash = App::getInstance()->getAuth()->generatePasswordHash( $password, $solt );

        $this->data['solt']     = $solt;
        $this->data['password'] = $hash;

        $this->markDirty();

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


    /**
     * Возвращает права пользователя
     * @return int
     */
    function getPermission()
    {
        return $this->data['perm'];
    }

    /**
     * Проверит наличие прав у пользователя
     * @param int $perm
     * @return bool
     */
    function hasPermission( $perm )
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
    function isPermission( $perm )
    {
        return $this->data['perm'] == $perm ? true : false;
    }


}
