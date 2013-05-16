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
    public function getId()
    {
        return $this->request->getSession()->get('user_id');
    }

    /**
     * Установит id авторизованного пользователя
     * @param  $id
     * @return void
     */
    public function setId($id)
    {
        $this->request->getSession()->set('user_id', $id);
    }
}
