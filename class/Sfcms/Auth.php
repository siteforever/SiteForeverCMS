<?php
namespace Sfcms;

use Module\User\Object\User;
use Sfcms\Data\DataManager;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Интерфейс авторизации
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Auth extends ContainerAware
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
     * @return DataManager
     */
    public function getDataManager()
    {
        return $this->container->get('data.manager');
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Текущий пользователь
     * @return User
     */
    public function currentUser()
    {
        if ($this->getId()) {
            $obj = $this->getDataManager()->getModel('User')->findByPk($this->getId());
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
        return $this->request ? $this->request->getSession()->get('user_id', null) : null;
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
        return $this->getId() && $this->currentUser()
            ? $this->currentUser()->getPermission()
            : USER_GUEST;
    }

    /**
     * @param $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (!$this->getId()) {
            if (in_array($permission, array(USER_GUEST, USER_ANONIMUS), true)) {
                return true;
            }
            return false;
        }
        if (!$this->currentUser()) {
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
        return $this->getDataManager()->getModel('User')->createObject(array(
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
