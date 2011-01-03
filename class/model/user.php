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

    function createTables()
    {
        $this->table    = new Data_Table_User();

        if ( ! $this->isExistTable( $this->table ) ) {
            $this->db->query($this->table->getCreateTable());

            $this->set('login', 'admin');
            $this->changePassword('admin');
            $this->set('perm', USER_ADMIN);
            $this->set('status', '1');
            $this->set('date',  time());
            $this->set('email', $this->config->get('admin'));
            $this->save();
        }
    }

    function __construct( $fail = true )
    {
        parent::__construct( $fail );

        if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] )
        {
            // ищем авторизованного пользователя
            $data = $this->find( $_SESSION['user_id'] );
            $data['last'] = time();
            $this->db->update( $this->table, $data, " id = {$data['id']} ", 1 );
            $this->data = $data;
        }
        else {
            $_SESSION['user_id'] = 0;
        }
    }

    /**
     * Поиск профиля по email
     * @param $email
     */
    function findByEmail( $email )
    {
        $data = $this->find(array(
            'cond'      => 'email = :email',
            'params'    => array(':email'=>$email),
        ));
        if ( $data ) {
            $this->setData($data);
            return true;
        }
        return false;
    }

    /**
     * Поиск всех юзеров
     * @param string $cond
     * @param string $order
     * @param string $limit
     */
    function findAll( $cond = '', $order = 'login', $limit = '' )
    {
        $where = '';
        if ( $cond ) {
            $where = " WHERE {$cond} ";
        }
        if ( $order ) {
            $order = " ORDER BY {$order} ";
        }
        return $this->db->fetchAll("SELECT * FROM {$this->table} {$where} {$order} {$limit}");
    }

    /**
     * Запись информации в базу
     * @return mixed
     */
    function update()
    {
        $data = $this->data;
        if ( empty( $data['password'] ) ) {
            unset( $data['password'] );
        }
        $ins = $this->db->insertUpdate($this->table, $data);
        if ( $ins ) {
            $this->set('id', $ins);
        }
        return $ins;
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
    function getBasketArray()
    {
        $basket = json_decode( $this->data['basket'], true );
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
    function setBasketFromArray( $array )
    {
        $basket = json_encode( $array );
        $this->data['basket'] = $basket;
        $this->update();
        //die( $this->data['basket'] );
    }

    /**
     * Регистрация
     * @param array $data
     */
    function register( $data )
    {
        $data['solt']   = $this->generateString( 8 );
        $data['password']= $this->generatePasswordHash( $data['password'], $data['solt'] );
        $data['perm']   = USER_GUEST;
        $data['status'] = 0;
        $data['confirm'] = md5(microtime());

        $data['login']  = $this->db->escape( $data['login'] );
        $data['date']   = time();
        $data['last']   = time();

        $user = $this->db->fetch("SELECT * FROM ".DBUSERS." WHERE login = '{$data['login']}' OR email = '{$data['email']}' LIMIT 1");
        if ( $user )
        {
            $this->request->addFeedback('Такой пользователь уже зарегистрирован');
            return false;
        }
        // безопастное добавление данных
        foreach( $this->data as $key => $item ) {
            if ( isset( $data[$key] ) ) {
                $this->data[$key] = $data[$key];
            }
        }

        if ( $this->update() )
        {
            $this->tpl->assign('data', $this->data);
            $this->tpl->assign('sitename', App::$config->get('sitename') );
            $this->tpl->assign('siteurl', App::$config->get('siteurl') );

            $msg = $this->tpl->fetch('system:users.register');

            //print $msg;

            sendmail(
                $this->config->get('sitename').' <'.$this->config->get('admin').'>',
                $data['email'],
                'Подтверждение регистрации',
                $msg
            );
            App::$request->addFeedback("Регистрация прошла успешно. На Ваш Email отправлена ссылка для подтверждения регистрации.");
            return true;
        }
        return false;
    }

    /**
     * Логин
     * @param $data
     */
    function login( $data )
    {
        if ( isset($data['login']) && isset($data['password']) )
        {
            $user = $this->db->fetch(
                "SELECT * FROM ".DBUSERS." WHERE login = :login LIMIT 1",
                DB::F_ASSOC,
                array(':login'=>$data['login'])
            );
            if ( $user )
            {
                if ( $user['perm'] < USER_USER ) {
                    $this->request->addFeedback('Не достаточно прав доступа');
                    return false;
                }
                if ( $user['status'] == 0 ) {
                    $this->request->addFeedback('Ваша учетная запись отключена');
                    return false;
                }

                $password = $this->generatePasswordHash( $data['password'], $user['solt'] );

                if ( $password != $user['password'] ) {
                    $this->request->addFeedback('Ваш пароль не подходит');
                    return false;
                }

                $_SESSION['user_id'] = $user['id'];
                $this->setData( $user );
                $this->request->addFeedback('Авторизация прошла успешно');
                return true;
            }
            $this->request->addFeedback('Ваш логин не зарегистрирован');
        }
        return false;
    }

    /**
     * Выход из системы
     * @return void
     */
    function logout()
    {
        $_SESSION['user_id'] = 0;
        $this->set('id', 0);
        $this->set('perm', USER_GUEST);
    }

    /**
     * Подтвердить регистрацию
     * @return void
     */
    function confirm()
    {
        $user_id = $this->request->get('userid', FILTER_VALIDATE_INT);
        $confirm = $this->db->escape( App::$request->get('confirm') );

        if ( $user_id && $confirm )
        {
            $user = $this->db->fetch("SELECT * FROM ".DBUSERS." WHERE id = {$user_id} AND confirm = '{$confirm}' LIMIT 1");
            if ( $user ) {
                $this->setData( $user );
                $this->set('perm', USER_USER);
                $this->set('status', 1);
                $this->set('last', time());

                $this->active();
                $this->update();

                $_SESSION['user_id'] = $user['id'];

                $this->request->addFeedback('Регистрация успешно подтверждена');
                return true;
            }
        }
        $this->request->addFeedback('Ваш аккаунт не подвержден, обратитесь к '.
                '<a href="mailto:'.App::$config->get('admin').'">администрации сайта</a>');
        return false;
    }

    /**
     * Активировать пользователя
     * @return void
     */
    function active()
    {
        if ( $this->get('id') ) {
            $this->set('status', 1);
        }
    }

    /**
     * Деактивировать пользователя
     * @return void
     */
    function deactive()
    {
        if ( $this->get('id') ) {
            $this->set('status', 0);
        }
    }

    /**
     * Поменять пароль пользователя
     * @param $password
     * @return void
     */
    function changePassword( $password )
    {
        $solt = $this->generateString(8);
        $hash = $this->generatePasswordHash( $password, $solt );
        $this->set('solt', $solt);
        $this->set('password', $hash);
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

}