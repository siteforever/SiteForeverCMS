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

        $obj->changePassword('admin');

        $this->save( $obj );
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
     * @param Data_Object|null $obj
     * @return void
     */
    function save( Data_Object $obj )
    {
        if ( empty( $obj->password ) ) {
            unset( $obj->password );
        }
        return parent::save( $obj );
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
    function setBasketFromArray( $array, Data_Object_User $obj )
    {
        $basket = json_encode( $array );
        $obj->basket    = $basket;
        //$this->data['basket'] = $basket;
        $this->save( $obj );
        //die( $this->data['basket'] );
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

    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_User';
    }
}