<?php
use Sfcms\Kernel\KernelBase as Service;
use Module\User\Model\UserModel;
use Module\User\Object\User;

// группы пользователей
define('USER_GUEST', '0'); // гость
define('USER_USER',  '1'); // юзер
define('USER_WHOLE', '2'); // оптовый покупатель
define('USER_ADMIN', '10'); // админ


/**
 * Интерфейс авторизации
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
abstract class Auth
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected   $message   = '';

    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @var UserModel
     */
    protected $model = null;

    /** @var  \Sfcms\Request */
    protected $request;

    public function __construct(\Sfcms\Request $request)
    {
        $this->request = $request;
        $this->model = \Sfcms\Model::getModel('User');

        if ($this->getId()) {
            $obj = $this->model->findByPk($this->getId());
            if ($obj) {
                $this->user = $obj;
                $this->user->last = time();
                return;
            }
        }
        $this->user =$this->model->createObject(array(
            'login' => 'guest',
            'perm'  => USER_GUEST,
        ));
    }

    /**
     * Текущий пользователь
     * @param User $user
     * @return User|void
     */
    public function currentUser($user = null)
    {
        if (null === $user) {
            return $this->user;
        }
        $this->user = $user;
        $this->setId($user->getId());
    }

    /**
     * Id текущего пользователя
     * @abstract
     * @return int
     */
    abstract public function getId();

    /**
     * Установит id авторизованного пользователя
     * @abstract
     * @param  $id
     * @return void
     */
    abstract public function setId( $id );


    /**
     * Выход из системы
     * @return void
     */
    public function logout()
    {
        $this->user =$this->model->createObject(array(
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
    public function generateString( $len, $pattern = '/[a-z0-9]/i' )
    {
        $c      = $len;
        $str    = '';
        while ( $c > 0 ) {
            $charcode = rand(33, 122);
            $chr = chr( $charcode );
            if ( preg_match($pattern, $chr) ) {
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
