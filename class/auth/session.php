<?php
/**
 * Авторизация на базе сессий
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Auth_Session extends Auth
{
    /**
     * Id текущего пользователя
     * @return int
     */
    function getId()
    {
        if ( empty( $_SESSION['user_id'] ) ) {
            $_SESSION['user_id']    = 0;
        }
        return $_SESSION['user_id'];
    }

    /**
     * Установит id авторизованного пользователя
     * @param  $id
     * @return void
     */
    function setId($id)
    {
        $_SESSION['user_id']    = $id;
    }
}
