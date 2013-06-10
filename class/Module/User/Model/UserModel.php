<?php
namespace Module\User\Model;

use Sfcms\Model;

use Sfcms\Form\Form;
use Sfcms\Data\Object;
use Module\User\Object\User;
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
            USER_GUEST  => t('user', 'Guest'),
            USER_USER   => t('user', 'User'),
            USER_WHOLE  => t('user', 'Whole user'),
            USER_ADMIN  => t('user', 'Admin'),
        );
    }

    /**
     * @return void
     */
    public function onCreateTable()
    {
        /** @var $obj User */
        $obj = $this->createObject(
            array(
                'login'  => 'admin',
                'perm'   => USER_ADMIN,
                'status' => '1',
                'date'   => time(),
                'email'  => $this->config->get('admin'),
            )
        );

        $obj->changePassword('admin');
        $obj->save();
    }

    /**
     * Поиск профиля по email
     * @param $email
     * @return Object
     */
    public function findByEmail( $email )
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
     * Hide password
     * @param Model\ModelEvent $event
     */
    public function onSaveStart(Model\ModelEvent $event)
    {
        $obj = $event->getObject();
        if (get_class($obj) == $this->objectClass()) {
            if (empty($obj->password)) {
                unset($obj->password);
            }
        }
    }

    /**
     * Вернет массив с корзиной
     * @deprecated
     * @return array
     */
    public function getBasketArray( Object $user )
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
    public function setBasketFromArray( $array, User $obj )
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
    public function getLoginForm()
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
    public function getProfileForm()
    {
        if ( is_null( $this->profile_form ) ) {
            $this->profile_form = new FormProfile();
        }
        return $this->profile_form;
    }

    public function getPasswordForm()
    {
        if ( is_null( $this->password_form ) ) {
            $this->password_form = new FormPassword();
        }
        return $this->password_form;
    }

    public function getRestoreForm()
    {
        if ( is_null( $this->restore_form ) ) {
            $this->restore_form = new FormRestore();
        }
        return $this->restore_form;
    }

    public function getRegisterForm()
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
    public function getEditForm()
    {
        if ( is_null( $this->edit_form ) ) {
            $this->edit_form = new Forms_User_Edit();
        }
        return $this->edit_form;

    }

}