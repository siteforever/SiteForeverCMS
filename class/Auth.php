<?php
use Sfcms\Kernel\KernelBase as Service;
use Module\User\Model\UserModel;
use Module\User\Object\User;
use Symfony\Component\Security\Core\Util\SecureRandom;


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

    /** @var UserModel */
    protected $model = null;

    /** @var  \Sfcms\Request */
    protected $request;

    public function __construct(\Sfcms\Request $request)
    {
        $this->request = $request;
        $this->model = \Sfcms\Model::getModel('User');

        if ($this->getId()) {
            /** @var User $obj */
            $obj = $this->model->findByPk($this->getId());
            if ($obj) {
                $obj->last = time();
                $this->currentUser($obj);
                return;
            }
        }
        $this->currentUser($this->createDefaultUser());
    }

    /**
     * Текущий пользователь
     * @param User $user
     * @return User|void
     */
    public function currentUser($user = null)
    {
        if (null !== $user) {
            $this->user = $user;
            $this->setId($user->getId());
        }
        return $this->user;
    }

    /**
     * Id текущего пользователя
     * @return int
     */
    private function getId()
    {
        return $this->request->getSession()->get('user_id');
    }

    /**
     * Установит id авторизованного пользователя
     * @param  $id
     * @return void
     */
    private function setId($id)
    {
        $this->request->getSession()->set('user_id', $id);
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
        $this->setId(0);
        $this->currentUser($this->createDefaultUser());
    }

    /**
     * @return User
     */
    private function createDefaultUser()
    {
        return $this->model->createObject(array(
                'login'  => 'guest',
                'perm'   => USER_GUEST,
            ));
    }

    /**
     * Генерирует случайную строку
     * @param string $len Length generated string
     * @param string $pattern Regexp for matches with generated string
     * @return string
     */
    public function generateString($len, $pattern = null)
    {
        $c   = $len;
        if (null === $pattern) {
            $generator = new SecureRandom();
            return $generator->nextBytes($len);
        }

        $str = '';
        while ($c > 0) {
            $charcode = rand(33, 122);
            $chr      = chr($charcode);
            if (preg_match($pattern, $chr)) {
                $str .= $chr;
                $c--;
            }
        }

        return $str;
    }

    /**
     * Генерирует хэш пароля
     * @param $password
     * @param $solt
     * @return string
     */
    public function generatePasswordHash( $password, $solt )
    {
        return md5( md5($solt) . md5($password) );
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
