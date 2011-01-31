<?php
/**
 * Интерфейс авторизации
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

abstract class Auth_Abstract
{
    /**
     * @var Data_Object_User
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
     * @var Model_User
     */
    protected $model = null;

    function __construct()
    {
        $this->model    = Model::getModel('User');

        if ( $this->getId() ) {
            $obj    = $this->model->find( (int) $this->getId() );
            $this->user = $obj;
            $this->user->last   = time();
        } else {
            $this->user =$this->model->createObject(array(
               'login'  => 'guest',
               'perm'   => USER_GUEST,
            ));
            $this->user->markClean();
        }
    }

    /**
     * Текущий пользователь
     * @return Data_Object_User
     */
    function currentUser()
    {
        return $this->user;
    }

    /**
     * Id текущего пользователя
     * @abstract
     * @return int
     */
    abstract function getId();

    /**
     * Установит id авторизованного пользователя
     * @abstract
     * @param  $id
     * @return void
     */
    abstract function setId( $id );

    /**
     * Логин
     * @param string $login
     * @param string $password
     */
    function login( $login, $password )
    {
        if ( $password == '' ) {
            $this->message  = t('Empty password');
            return false;
        }

        $user = $this->model->find(array(
            'cond' => 'login = :login',
            'params'=> array(':login'=>$login),
        ));

        if ( $user )
        {
            //print_r( $user->getAttributes() );
            if ( $user->perm < USER_USER ) {
                $this->error    = true;
                $this->message  = t('Not enough permissions');
                return false;
            }
            if ( $user['status'] == 0 ) {
                $this->error    = true;
                $this->message  = t('Your account has been disabled');
                return false;
            }

            $password = $this->generatePasswordHash( $password, $user->solt );

            print $user->password.' == '.$password;

            if ( $password != $user->password ) {
                $this->error    = true;
                $this->message  = t('Your password is not suitable');
                return false;
            }

            $this->setId( $user->id );

            if ( $user->perm == USER_ADMIN ) {
                // Авторизация Sypex Dumper
                $_SESSION['sxd_auth']   = 1;
                $_SESSION['sxd_conf']   = CONFIG;
            }

            $this->error    = false;
            $this->message  = t('Authorization was successful');
            return true;
        }

        $this->error    = true;
        $this->message  = t('Your login is not registered');
    }



    /**
     * Выход из системы
     * @return void
     */
    function logout()
    {
        $this->setId(0);
        $_SESSION['sxd_auth']   = 0; // Авторизация Sypex Dumper
        $_SESSION['sxd_conf']   = null;
        setcookie('sxd', null, null, '/misc/sxd/');
        $this->user->id     = 0;
        $this->user->perm   = USER_GUEST;
        $this->user->markClean();
    }

    /**
     * Регистрация
     * @param Data_Object_User $obj
     */
    function register( Data_Object_User $obj )
    {
        $obj['solt']   = $this->generateString( 8 );
        $obj['password']= $this->generatePasswordHash( $obj['password'], $obj['solt'] );
        $obj['perm']   = USER_GUEST;
        $obj['status'] = 0;
        $obj['confirm'] = md5(microtime().$obj['solt']);

        //$data['login']  = $data['login'];
        $obj['date']   = time();
        $obj['last']   = time();

        $user   = $this->model->find(array(
             'cond'     => 'login = :login OR email = :email',
             'params'   => array(':login'=>$obj['login'], ':email'=>$obj['email']),
          ));

        if ( $user )
        {
            $this->error    = true;
            $this->message  = 'Такой пользователь уже зарегистрирован';
            return false;
        }

        //$this->setData( $data );
        // Надо сохранить, чтобы знать id
        if ( $this->model->save( $obj ) )
        {
            App::$tpl->data = $obj;
            App::$tpl->sitename = App::$config->get('sitename');
            App::$tpl->siteurl  = App::$config->get('siteurl');

            $msg = App::$tpl->fetch('system:users.register');

            //print $msg;

            sendmail(
                App::$config->get('sitename').' <'.App::$config->get('admin').'>',
                $obj->email,
                'Подтверждение регистрации',
                $msg
            );

            $this->error    = false;
            $this->message  = "Регистрация прошла успешно. ".
                              "На Ваш Email отправлена ссылка для подтверждения регистрации.";
            return true;
        }
        return false;
    }


    /**
     * Подтвердить регистрацию
     * @return void
     */
    function confirm()
    {
        /**
         * @var Data_Object_User $user
         */
        $user_id = App::getInstance()->getRequest()->get('userid', FILTER_VALIDATE_INT);
        $confirm = App::getInstance()->getRequest()->get('confirm');

        if ( $user_id && $confirm )
        {
            $user   = $this->model->find( array(
                    'cond'      => 'id = :id AND confirm = :confirm',
                    'params'    => array(':id'=>$user_id, ':confirm'=>$confirm),
               ) );

            if ( $user ) {
                $user->perm = USER_USER;
                $user->last = time();
                $user->active();

                $_SESSION['user_id'] = $user->getId();

                $this->error    = false;
                $this->message  = 'Регистрация успешно подтверждена';
                return true;
            }
        }

        $this->error    = true;
        $this->message  = 'Ваш аккаунт не подвержден, обратитесь к '.
                '<a href="mailto:'.App::$config->get('admin').'">администрации сайта</a>';
        return false;
    }

    /**
     * Возвращает права пользователя
     * @return int
     */
    function getPermission()
    {
        return $this->currentUser()->perm;
    }

    /**
     * Генерирует случайную строку
     * @param $len
     */
    function generateString( $len, $pattern = '/[a-z0-9]/i' )
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
     */
    function generatePasswordHash( $password, $solt )
    {
        return md5( md5($solt) . md5($password) );
    }

    function getMessage()
    {
        return $this->message;
    }

    function getError()
    {
        return $this->error;
    }

}