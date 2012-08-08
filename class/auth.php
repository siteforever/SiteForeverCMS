<?php

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
        $this->model    = $this->app()->getModel('User');

        if ( $this->getId() ) {
            $obj                = $this->model->find( (int) $this->getId() );
            if ( $obj ) {
                $this->user         = $obj;
                if ( ! $this->app()->getRequest()->isAjax() && $this->user->last + 600 < time() ) {
                    $this->user->last   = time();
                    $this->user->markDirty();
                }
                return;
            }
        }
        $this->user =$this->model->createObject(array(
            'login' => 'guest',
            'perm'  => USER_GUEST,
        ));
        $this->user->markClean();
    }

    /**
     * @return Application_Abstract
     */
    function app()
    {
        return App::getInstance();
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
            'cond' => 'login = ?',
            'params'=> array($login),
        ));

        if ( $user ) {
            //print_r( $user->getAttributes() );
            if ( $user->perm < USER_USER ) {
                $this->error    = true;
                $this->message  = t('Not enough permissions');
                return false;
            }

            if ( $user->status == 0 ) {
                $this->error    = true;
                $this->message  = t('Your account has been disabled');
                return false;
            }

            $password = $this->generatePasswordHash( $password, $user->solt );

            //print $user->password.' == '.$password;

            if ( $password != $user->password ) {
                $this->error    = true;
                $this->message  = t('Your password is not suitable');
                return false;
            }

            $this->user = $user;

            $this->setId( $user->getId() );

            if ( $user->perm == USER_ADMIN ) {
                // Авторизация Sypex Dumper
                $_SESSION['sxd_auth']   = 1;
                $_SESSION['sxd_conf']   = $this->app()->getConfig()->get('db');
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
        setcookie('sxd', null, null, '/_runtime/sxd/');
        $this->user =$this->model->createObject(array(
            'login'  => 'guest',
            'perm'   => USER_GUEST,
        ));
        $this->user->markClean();
    }

    /**
     * Регистрация
     * @param Data_Object_User $obj
     */
    function register( Data_Object_User $obj )
    {
        if ( strlen( $obj['login'] ) < 5 ) {
            $this->setError('Логин должен быть не короче 5 символов');
            return false;
        }

        if ( strlen( $obj['password'] ) < 6 ) {
            $this->setError( 'Пароль должен быть не короче 6 символов' );
            return false;
        }

        $obj['solt']    = $this->generateString( 8 );
        $obj['password']= $this->generatePasswordHash( $obj['password'], $obj['solt'] );
        $obj['perm']    = USER_GUEST;
        $obj['status']  = 0;
        $obj['confirm'] = md5(microtime().$obj['solt']);

        //$data['login']  = $data['login'];
        $obj['date']   = time();
        $obj['last']   = time();

        $user   = $this->model->find(array(
             'cond'     => 'login = :login',
             'params'   => array(':login'=>$obj['login']),
          ));

        if ( $user ) {
            $this->setError( 'Пользователь с таким логином уже существует' );
            return false;
        }

        $user   = $this->model->find(array(
            'cond'     => 'email = :email',
            'params'   => array(':email'=>$obj['email']),
        ));

        if ( $user ) {
            $this->setError( 'Пользователь с таким адресом уже существует' );
            return false;
        }

        //printVar($obj->getAttributes());

        //return;

        //$this->setData( $data );
        // Надо сохранить, чтобы знать id
        if ( $this->model->save( $obj ) ) {
            $tpl    = $this->app()->getTpl();
            $config = $this->app()->getConfig();

            $tpl->data = $obj;
            $tpl->sitename = $config->get('sitename');
            $tpl->siteurl  = $config->get('siteurl');

            $msg = $tpl->fetch('system:users.mail.register');

            //print $msg;

            sendmail(
                $config->get('sitename').' <'.$config->get('admin').'>',
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
        $request    = $this->app()->getRequest();

        /**
         * @var Data_Object_User $user
         */
        $user_id = $request->get('userid', FILTER_VALIDATE_INT);
        $confirm = $request->get('confirm');

        if ( $user_id && $confirm )
        {
            $user   = $this->model->find( array(
                    'cond'      => 'id = :id AND confirm = :confirm',
                    'params'    => array(':id'=>$user_id, ':confirm'=>$confirm),
               ));

            if ( $user ) {
                $user->perm = USER_USER;
                $user->last = time();
                $user->active();
                $user->confirm  = md5(microtime().$user->solt);

                //$this->setId( $user->getId() );

                $this->error    = false;
                $this->message  = 'Регистрация успешно подтверждена';
                return true;
            }
        }

        $this->error    = true;
        $this->message  = 'Ваш аккаунт не подвержден, обратитесь к '.
                '<a href="mailto:'.$this->app()->getConfig()->get('admin').'">администрации сайта</a>';
        return false;
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
     */
    function generatePasswordHash( $password, $solt )
    {
        return md5( md5($solt) . md5($password) );
    }

    /**
     * Вернет сообщение системы
     * @return string
     */
    function getMessage()
    {
        return $this->message;
    }

    /**
     * Вернет состояние ошибки
     * @return bool
     */
    function getError()
    {
        return $this->error;
    }

    /**
     * Обозначить ошибку
     * @param string $message
     * @return void
     */
    function setError( $message )
    {
        $this->message  = $message;
        $this->error    = true;
    }

}
