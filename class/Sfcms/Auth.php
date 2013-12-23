<?php
namespace Sfcms;

use Module\User\Object\User;

/**
 * Интерфейс авторизации
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Auth
{
    /** @var User */
    protected $user;

    /** @var string */
    protected $message   = '';

    /** @var bool */
    protected $error = false;

    /** @var  Request */
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
// todo по идее надо разлогинить, но, тогда тесты отваливаются
//        $this->logout();
        $this->request = $request;
    }

    /**
     * Текущий пользователь
     * @return User
     */
    public function currentUser()
    {
        if ($this->getId()) {
            $obj = Model::getModel('User')->findByPk($this->getId());
            if ($obj) {
                return $obj;
            } else {
                $this->setId(null);
            }
        }
        return null;
    }

    /**
     * @param User $user
     */
    public function setCurrentUser(User $user = null)
    {
        if (null === $user) {
            $this->setId(null);
        } else {
            $this->setId($user->id);
        }
    }

    /**
     * Id текущего пользователя
     * @return int
     */
    public function getId()
    {
        return $this->request->getSession()->get('user_id', null);
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

    /**
     * @return int|string
     */
    public function getPermission()
    {
        return $this->getId() ? $this->currentUser()->getPermission() : USER_GUEST;
    }

    public function hasPermission($permission)
    {
        if (!$this->getId()) {
            if (in_array($permission, array(USER_GUEST, USER_ANONIMUS), true)) {
                return true;
            }
            return false;
        }
        return $this->currentUser()->hasPermission($permission);
    }

    /**
     * @return bool
     */
    public function isLogged()
    {
        return null !== $this->getId();
    }

    /**
     * Выход из системы
     * @return void
     */
    public function logout()
    {
        $this->setId(null);
    }

    /**
     * @return User
     */
    protected function createDefaultUser()
    {
        return Model::getModel('User')->createObject(array(
                'login'  => 'guest',
                'perm'   => USER_GUEST,
            ));
    }

    /**
     * Вернет сообщение системы
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Вернет состояние ошибки
     * @return bool
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Обозначить ошибку
     * @param string $message
     * @return void
     */
    public function setError( $message )
    {
        $this->message  = $message;
        $this->error    = true;
    }

}
