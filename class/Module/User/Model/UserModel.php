<?php
namespace Module\User\Model;

use Module\User\Exception\UserException;
use Module\User\Form\PasswordForm;
use Module\User\Form\ProfileForm;
use Module\User\Form\RegisterForm;
use Module\User\Form\RestoreForm;
use Module\User\Form\UserEditForm;
use Sfcms\Model;

use Sfcms\Form\Form;
use Sfcms\Data\Object;
use Module\User\Object\User;

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
            USER_GUEST  => $this->t('user', 'Guest'),
            USER_USER   => $this->t('user', 'User'),
            USER_WHOLE  => $this->t('user', 'Whole user'),
            USER_ADMIN  => $this->t('user', 'Admin'),
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
                'email'  => \App::cms()->getContainer()->getParameter('admin'),
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
        if (get_class($obj) == $obj->getModel()->objectClass()) {
            if (empty($obj->password)) {
                unset($obj->password);
            }
        }
    }

    /**
     * Форма профиля пользователя на сайте
     * @return Form
     */
    public function getProfileForm()
    {
        if ( is_null( $this->profile_form ) ) {
            $this->profile_form = new ProfileForm();
        }
        return $this->profile_form;
    }

    public function getPasswordForm()
    {
        if ( is_null( $this->password_form ) ) {
            $this->password_form = new PasswordForm();
        }
        return $this->password_form;
    }

    public function getRestoreForm()
    {
        if ( is_null( $this->restore_form ) ) {
            $this->restore_form = new RestoreForm();
        }
        return $this->restore_form;
    }

    public function getRegisterForm()
    {
        if ( is_null( $this->register_form ) ) {
            $this->register_form = new RegisterForm();
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
            $this->edit_form = new UserEditForm();
        }
        return $this->edit_form;

    }


    /**
     * Регистрация
     * @param User $user
     * @param $group
     * @return bool
     * @throws UserException
     */
    public function register(User $user, $group = USER_GUEST, $status = 0)
    {
        if (strlen($user->login) < 5) {
            throw new UserException('Логин должен быть не короче 5 символов');
        }

        if (strlen($user->password) < 6) {
            throw new UserException('Пароль должен быть не короче 6 символов');
        }

        $user->solt = $user->generateString(8);
        $user->password = $user->generatePasswordHash($user->password, $user->solt);
        $user->perm = $group;
        $user->status = $status;
        $user->confirm = md5($user->solt . md5(microtime(1) . $user->solt));
        $user->date = $user->last = time();

        $userLogin = $this->find(array(
            'cond' => 'login = :login',
            'params' => array(':login' => $user->login),
        ));

        if ($userLogin) {
            throw new UserException(sprintf('Пользователь с логином "%s" уже существует', $user->login));
        }

        $userEmail = $this->find(array(
            'cond' => 'email = :email',
            'params' => array(':email' => $user['email']),
        ));

        if ($userEmail) {
            throw new UserException(sprintf('Пользователь с адресом "%s" уже существует', $user->email));
        }

        // Надо сохранить, чтобы знать id
        if ($this->save($user)) {
            return true;
        }

        return false;
    }
}
