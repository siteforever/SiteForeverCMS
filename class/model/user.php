<?php
class model_User extends Model
{
    /**
     * Форма входа в систему
     * @var form_Form
     */
    protected $login_form;
    /**
     * форма регистрации
     * @var form_Form
     */
    protected $register_form;
    /**
     * форма редактирования
     * @var form_Form
     */
    protected $edit_form;

    /**
     * Форма изменения профиля
     * @var form_Form
     */
    protected $profile_form;

    /**
     * Форма редактирования пароля
     * @var form_Form
     */
    protected $password_form;

    function onCreateTable()
    {
        $obj    = $this->createObject(array(
                'login'     => 'admin',
                'perm'      => USER_ADMIN,
                'status'    => '1',
                'date'      => time(),
                'email'     => $this->config->get('admin'),
          ));

        $this->changePassword('admin', $obj);

        $this->save( $obj );
    }

    /**
     * Вернет объект текущего пользователя
     * @static
     * @return Data_Object
     */
    static public function getCurrentUser()
    {
        $model  = self::getModel('model_User');
        if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] )
        {
            // ищем авторизованного пользователя
            $data = $model->find( (int) $_SESSION['user_id'] );
            $data['last'] = time();
            $model->save( $data );
            //$this->db->update( $this->table, $data, " id = {$data['id']} ", 1 );
            //$this->data = $data;
            return $data;
        }
        else {
            $_SESSION['user_id'] = 0;
            return $model->createObject(array(
                               'id'     => '0',
                               'login'  => 'guest',
                               'perm'   => USER_GUEST,
                          ));
        }

    }

    /**
     * Инициализация
     * @return void
     */
    function Init()
    {
    }

    /**
     * Поиск профиля по email
     * @param $email
     * @return Data_Object
     */
    function findByEmail( $email )
    {
        $data = $this->find(array(
            'cond'      => 'email = :email',
            'params'    => array(':email'=>$email),
        ));
        if ( $data ) {
            //$this->setData($data);
            return $data;
        }
        return null;
    }

    /**
     * Поиск всех юзеров
     * @param string $cond
     * @param string $order
     * @param string $limit
     */
    /*function findAll( $cond = '', $order = 'login', $limit = '' )
    {
        $where = '';
        if ( $cond ) {
            $where = " WHERE {$cond} ";
        }
        if ( $order ) {
            $order = " ORDER BY {$order} ";
        }
        return $this->db->fetchAll("SELECT * FROM {$this->table} {$where} {$order} {$limit}");
    }*/

    /**
     * Запись информации в базу
     * @return mixed
     */
    function update( Data_Object $obj )
    {
        return $this->save( $obj );
    }

    /**
     * @param Data_Object|null $obj
     * @return void
     */
    function save( Data_Object $obj )
    {
        if ( empty( $obj->password ) ) {
            unset( $obj->password );
        }
        parent::save( $obj );
    }

    /**
     * Количество пользователей в базе
     * @param $cond
     * @return mixed
     */
    function count($cond = '')
    {
        $where = '';
        if ( $cond ) {
            $where = " WHERE {$cond} ";
        }
        return $this->db->fetchOne("SELECT COUNT(*) FROM {$this->table} $where ");
    }


    /**
     * Права пользователя
     */
    function getPermission()
    {
        if ( isset($this->data['perm']) ) {
            return $this->data['perm'];
        }
        return USER_GUEST;
    }

    /**
     * Вернет массив с корзиной
     * @return array
     */
    function getBasketArray( Data_Object $user )
    {
        $basket = json_decode( $user['basket'], true );
        if ( $basket ) {
            return $basket;
        }
        return array();
    }

    /**
     * Установить новые значения для корзины
     * @param array $array
     * @return void
     */
    function setBasketFromArray( $array, Data_Object $obj )
    {
        $basket = json_encode( $array );
        $obj->basket    = $basket;
        //$this->data['basket'] = $basket;
        $this->save( $obj );
        //die( $this->data['basket'] );
    }

    /**
     * Регистрация
     * @param Data_Object $obj
     */
    function register( Data_Object $obj )
    {
        $obj['solt']   = $this->generateString( 8 );
        $obj['password']= $this->generatePasswordHash( $obj['password'], $obj['solt'] );
        $obj['perm']   = USER_GUEST;
        $obj['status'] = 0;
        $obj['confirm'] = md5(microtime());

        //$data['login']  = $data['login'];
        $obj['date']   = time();
        $obj['last']   = time();

        $user   = $this->find(array(
             'cond'     => 'login = :login OR email = :email',
             'params'   => array(':login'=>$obj['login'], ':email'=>$obj['email']),
          ));

        if ( $user )
        {
            $this->request->addFeedback('Такой пользователь уже зарегистрирован');
            return false;
        }

        //$this->setData( $data );

        if ( $this->save( $obj ) )
        {
            $this->tpl->data    = $obj;
            $this->tpl->sitename= $this->config->get('sitename');
            $this->tpl->siteurl = $this->config->get('siteurl');

            $msg = $this->tpl->fetch('system:users.register');

            //print $msg;

            sendmail(
                $this->config->get('sitename').' <'.$this->config->get('admin').'>',
                $obj->email,
                'Подтверждение регистрации',
                $msg
            );
            $this->request->addFeedback("Регистрация прошла успешно. На Ваш Email отправлена ссылка для подтверждения регистрации.");
            return true;
        }
        return false;
    }

    /**
     * Логин
     * @param string $login
     * @param string $password
     */
    function login( $login, $password )
    {
        $user = $this->find(array(
            'cond' => 'login = :login',
            'params'=> array(':login'=>$login),
        ));

        if ( $user )
        {
            if ( $user['perm'] < USER_USER ) {
                $this->request->addFeedback(t('Not enough permissions'));
                return false;
            }
            if ( $user['status'] == 0 ) {
                $this->request->addFeedback(t('Your account has been disabled'));
                return false;
            }

            $password = $this->generatePasswordHash( $password, $user['solt'] );

            if ( $password != $user['password'] ) {
                $this->request->addFeedback(t('Your password is not suitable'));
                return false;
            }

            $_SESSION['user_id'] = $user['id'];
            //$this->setData( $user );
            $this->request->addFeedback(t('Authorization was successful'));
            return true;
        }
        $this->request->addFeedback(t('Your login is not registered'));
    }

    /**
     * Выход из системы
     * @return void
     */
    function logout()
    {
        $_SESSION['user_id'] = 0;
        App::$user->id      = 0;
        App::$user->perm    = USER_GUEST;
    }

    /**
     * Подтвердить регистрацию
     * @return void
     */
    function confirm()
    {
        $user_id = $this->request->get('userid', FILTER_VALIDATE_INT);
        $confirm = $this->request->get('confirm');

        if ( $user_id && $confirm )
        {
            $user   = $this->find( array(
                    'cond'      => 'id = :id AND confirm = :confirm',
                    'params'    => array(':id'=>$user_id, ':confirm'=>$confirm),
               ) );
            if ( $user ) {
                $user->perm = USER_USER;
                $user->last = time();

                $this->active( $user );
                $this->save( $user );

                $_SESSION['user_id'] = $user['id'];

                $this->request->addFeedback('Регистрация успешно подтверждена');
                return true;
            }
        }
        $this->request->addFeedback('Ваш аккаунт не подвержден, обратитесь к '.
                '<a href="mailto:'.$this->config->get('admin').'">администрации сайта</a>');
        return false;
    }

    /**
     * Активировать пользователя
     * @return void
     */
    function active( Data_Object $obj )
    {
        if ( $obj->id ) {
            $obj->status = 1;
        }
    }

    /**
     * Деактивировать пользователя
     * @return void
     */
    function deactive( Data_Object $obj )
    {
        if ( $obj->id ) {
            $obj->status = 0;
        }
    }

    /**
     * Поменять пароль пользователя
     * @param string $password
     * @param Data_Object $obj
     * @return void
     */
    function changePassword( $password, Data_Object $obj = null )
    {
        $solt = $this->generateString(8);
        $hash = $this->generatePasswordHash( $password, $solt );
        if ( is_null( $obj ) ) {
            $this->set('solt', $solt);
            $this->set('password', $hash);
        }
        else {
            $obj->solt      = $solt;
            $obj->password  = $hash;
        }
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

    /**
     * Вернет форму для логина
     * @return form_Form
     */
    function getLoginForm()
    {
        if ( is_null( $this->login_form ) ) {
            $this->login_form = new form_Form(array(
                'name'      => 'login',
                'action'    => App::$router->createLink('users/login'),
                'fields'    => array(
                    'login'     => array('type'=>'text',    'label'=>'Логин'),
                    'password'  => array('type'=>'password','label'=>'Пароль'),
                    'submit'    => array('type'=>'submit', 'value'=>'Войти'),
                ),
            ));
        }
        return $this->login_form;
    }

    /**
     * Форма профиля пользователя на сайте
     * @return form_Form
     */
    function getProfileForm()
    {
        if ( is_null( $this->profile_form ) ) {
            $this->profile_form = new form_Form(array(
                'name'      => 'profile',
                'class'     => 'standart',
                'fields'    => array(
                    'id'        => array('type'=>'hidden',),
                    'fname'     => array('type'=>'text', 'label'=>'Имя'),
                    'lname'     => array('type'=>'text', 'label'=>'Фамилия'),
                    'email'     => array('type'=>'text', 'label'=>'Email', 'required'),
                    'name'      => array('type'=>'text', 'label'=>'Наименование организации **'),
                    'phone'     => array('type'=>'text', 'label'=>'Телефон **'),
                    'fax'       => array('type'=>'text', 'label'=>'Факс',),
                    'inn'       => array('type'=>'text', 'label'=>'ИНН **',),
                    'kpp'       => array('type'=>'text', 'label'=>'КПП **',),
                    'address'   => array('type'=>'textarea', 'label'=>'Адрес'),

                    'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
                ),
            ));
        }
        return $this->profile_form;
    }

    function getPasswordForm()
    {
        if ( is_null( $this->password_form ) ) {
            $this->password_form = new form_Form(array(
                'name'      => 'password',
                'fields'    => array(
                    'password'  => array('type'=>'password', 'label'=>'Старый пароль', 'required', 'autocomplete'=>'off'),
                    'password1' => array('type'=>'password', 'label'=>'Новый пароль',  'required', 'autocomplete'=>'off'),
                    'password2' => array('type'=>'password', 'label'=>'Повтор пароля', 'required', 'autocomplete'=>'off'),
                    'submit'    => array('type'=>'submit', 'value'=>'Изменить'),
                ),
            ));
        }
        return $this->password_form;
    }

    function getRegisterForm()
    {
        if ( is_null( $this->register_form ) ) {
            $this->register_form = new form_Form(array(
                'name'      => 'register',
                'class'     => 'standart',
                'fields'    => array(
                    'email'     => array('type'=>'text',    'label'=>'Email', 'required', 'autocomplete'=>'off'),
                    'login'     => array('type'=>'text',    'label'=>'Логин', 'required', 'autocomplete'=>'off'),
                    'password'  => array('type'=>'password', 'label'=>'Пароль', 'required', 'autocomplete'=>'off'),
                    'submit'    => array('type'=>'submit', 'value'=>'Регистрация'),
                ),
            ));
        }
        return $this->register_form;
    }

    /**
     * Форма редактирования
     * @return form_Form
     */
    function getEditForm()
    {
        if ( is_null( $this->edit_form ) ) {
            $this->edit_form = new forms_user_edit();
        }
        return $this->edit_form;

    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_User';
    }
}