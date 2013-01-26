<?php
namespace Module\System\Model;

use Sfcms\Model;

use Sfcms\Form\Form;
use Sfcms\Data\Object;
use Module\System\Object\User;
use Forms_User_Edit;
use Forms_User_Register;
use Forms\User\Login as FormLogin;
use Forms\User\Profile as FormProfile;
use Forms\User\Restore as FormRestore;
use Forms\User\Password as FormPassword;

class UserModel extends Model
{
    /**
     * Форма входа в систему
     * @var Form
     */
    protected $login_form;
    /**
     * Форма восстановления пароля
     * @var Form
     */
    protected $restore_form;
    /**
     * форма регистрации
     * @var Form
     */
    protected $register_form;
    /**
     * форма редактирования
     * @var Form
     */
    protected $edit_form;

    /**
     * Форма изменения профиля
     * @var Form
     */
    protected $profile_form;

    /**
     * Форма редактирования пароля
     * @var Form
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
     * @return Object
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
     * @param Object|null $obj
     * @return int
     */
    public function save( Object $obj )
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
    function getBasketArray( Object $user )
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
     * @param User $obj
     * @return void
     * @deprecated
     */
    function setBasketFromArray( $array, User $obj )
    {
        $basket = json_encode( $array );
        $obj->basket    = $basket;
        //$this->data['basket'] = $basket;
        $this->save( $obj );
        //die( $this->data['basket'] );
    }

    /**
     * Вернет форму для логина
     * @return Form
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
     * @return Form
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
            $this->register_form = new Forms_User_Register();
        }
        return $this->register_form;
    }

    /**
     * Форма редактирования
     * @return Form
     */
    function getEditForm()
    {
        if ( is_null( $this->edit_form ) ) {
            $this->edit_form = new Forms_User_Edit();
        }
        return $this->edit_form;

    }

}