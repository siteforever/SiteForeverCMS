<?php
use Forms\User\Login as FormLogin;
use Forms\User\Profile as FormProfile;
use Forms\User\Restore as FormRestore;
use Forms\User\Password as FormPassword;

class Model_User extends Sfcms_Model
{
    /**
     * Форма входа в систему
     * @var Form_Form
     */
    protected $login_form;
    /**
     * Форма восстановления пароля
     * @var Form_Form
     */
    protected $restore_form;
    /**
     * форма регистрации
     * @var Form_Form
     */
    protected $register_form;
    /**
     * форма редактирования
     * @var Form_Form
     */
    protected $edit_form;

    /**
     * Форма изменения профиля
     * @var Form_Form
     */
    protected $profile_form;

    /**
     * Форма редактирования пароля
     * @var Form_Form
     */
    protected $password_form;

    /**
     * Группы пользователей
     * @return array
     */
    public function getGroups()
    {
        return array(
            USER_GUEST  => 'Гость',
            USER_USER   => 'Пользователь',
            USER_WHOLE  => 'Постоянный покупатель',
            USER_ADMIN  => 'Админ',
        );
    }

    /**
     * @return void
     */
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
     * @return int
     */
    public function save( Data_Object $obj )
    {
        if ( empty( $obj->password ) ) {
            unset( $obj->password );
        }
        return parent::save( $obj );
    }

    /**
     * Вернет массив с корзиной
     * @deprecated
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
     * @param Data_Object_User $obj
     * @return void
     * @deprecated
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
            $this->login_form = new FormLogin();
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
            $this->profile_form = new FormProfile();
        }
        return $this->profile_form;
    }

    function getPasswordForm()
    {
        if ( is_null( $this->password_form ) ) {
            $this->password_form = new FormPassword();
        }
        return $this->password_form;
    }

    function getRestoreForm()
    {
        if ( is_null( $this->restore_form ) ) {
            $this->restore_form = new FormRestore();
        }
        return $this->restore_form;
    }

    function getRegisterForm()
    {
        if ( is_null( $this->register_form ) ) {
            $this->register_form = new forms_user_register();
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
            $this->edit_form = new Forms_User_Edit();
        }
        return $this->edit_form;

    }

}