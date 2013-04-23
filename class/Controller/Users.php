<?php
/**
 * Контроллер управления пользователями
 */

use Forms\User\Restore as FormRestore;
use Module\System\Model\UserModel;

class Controller_Users extends Sfcms_Controller
{
    /**
     * Инициализация
     */
    public function init()
    {
        $this->request->set('template', 'inner' );
    }

    /**
     * Управление доступом
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN  => array('admin','adminEdit','save'),
            USER_USER   => array('edit', 'cabinet'),
        );
    }

    /**
     * Основное действие
     * Выводит форму логина либо подтверждает регистрацию
     * @params string $confirm
     * @params int $userid
     *
     * @return mixed
     */
    public function indexAction( $confirm, $userid )
    {
        $auth   = $this->app()->getAuth();
        // подтверждение регистрации
        if ( $confirm && $userid ) {
            $this->request->setTitle('Подтверждение регистрации');
            $this->getTpl()->getBreadcrumbs()
                ->addPiece('index',t('Home'))
                ->addPiece('users',t('user','Sign in site'))
                ->addPiece(null,$this->request->getTitle());
            $auth->confirm();
            if ( $auth->getError() ) {
                return array('error' => 1, 'msg' => $auth->getMessage());
            }
            return array('success' => 1, 'msg' => $auth->getMessage());
        }

        return $this->loginAction();
    }


    /**
     * @return array
     * @throws Sfcms_Http_Exception
     */
    public function cabinetAction()
    {
        $auth   = $this->app()->getAuth();
        $user   = $auth->currentUser();

        $this->tpl->getBreadcrumbs()->addPiece('index',t('Home'))->addPiece(null,t('user','User cabiner'));

        if ( $user->getId() ) {
            // отображаем кабинет
            $this->request->setTitle(t('user','User cabiner'));
            return array( 'user' => $user );
        }
        throw new Sfcms_Http_Exception(t('Access denied'), 403);
    }


    /**
     * Управление пользователем
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->set('template', 'index' );

        // используем шаблон админки
        $this->request->setTitle(t('user','Users'));

        $model  = $this->getModel('user');

        $users = $this->request->get('users');

        if ( $users ) {
            foreach( $users as $key => $user ) {
                if ( isset( $user['delete'] ) ) {
                    $model->delete($key);
                    $this->request->addFeedback(t('user','Deleted user #').$key);
                    continue;
                }
            }
        }

        $search = $this->request->get('search');

        $criteria   = array(
            'cond'  => '',
            'params'=> array(),
        );

        if ( $search ) {
            if ( strlen($search) >= 2 ) {
                $criteria['cond']   =   ' login LIKE :search OR email LIKE :search '.
                                        ' OR lname LIKE :search OR name LIKE :search ';
                $criteria['params'][':search']  = '%'.$search.'%';
            } else {
                $this->request->addFeedback(t('user','Too short query'));
            }
        }

        $count  = $model->count( $criteria['cond'], $criteria['params'] );

        $paging = $this->paging($count, 25, '/admin/users/page%page%');

        $criteria['limit']  = $paging['offset'].', '.$paging['perpage'];
        $criteria['order']  = 'login';

        $users = $model->findAll($criteria);

        $this->tpl->assign('users', $users);
        $this->tpl->assign('paging', $paging);
        $this->tpl->assign('groups', $this->config->get('users.groups'));

        return $this->tpl->fetch('system:users.admin');
    }

    /**
     * Редактирование пользователя в админке
     * @params int $id
     * @return mixed
     */
    public function adminEditAction( $id )
    {
        /**
         * @var UserModel $model
         * @var Data_Object_User $user
         * @var form_Form $userForm
         */
        $model  = $this->getModel('user');

        $userForm = $model->getEditForm();

        if ( $id && $User = $model->find( $id ) )
        {
            $userForm->setData( $User->getAttributes() );
            $userForm->getField('password')->setValue('');
        }

        $this->request->set('template', 'index');
        $this->request->setTitle((!$id ? t('user','Add user') : t('user','Edit user') ));

        return array(
            'form' => $userForm,
        );
    }


    /**
     * @return mixed
     */
    public function saveAction()
    {
        /**
         * @var UserModel $model
         * @var Data_Object_User $User
         * @var Form_Form $userForm
         */
        $model  = $this->getModel('User');

        $userForm = $model->getEditForm();

        if ( $userForm->getPost() ) {

            if ( $userForm->validate() ) {
                $User = ($user_id = $userForm['id']) ? $model->find( $user_id ) : $model->createObject();
                $password = $User->password;
                $solt     = $User->solt;
                $User->attributes = $userForm->getData();

                if ( $userForm['password'] ) {
                    $User->changePassword( $userForm['password'] );
                } else {
                    $User->password = $password;
                    $User->solt     = $solt;
                }
                return array('error'=>0,'msg'=>t('Data save successfully'));
            } else {
                return array('error'=>1,'msg'=>$userForm->getFeedbackString());
            }
        }
        return t('Data not sent');
    }

    /**
     * Выход
     */
    public function logoutAction()
    {
        $this->app()->getAuth()->logout();
        return $this->redirect('users/login');
    }

    /**
     * Вход
     * @return mixed
     */
    public function loginAction()
    {
        $this->request->setTitle(t('Personal page'));
        /** @var UserModel $model */
        $model  = $this->getModel('User');
        $auth   = $this->app()->getAuth();

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece(null, t('user','Sign in site'));

        $user   = $auth->currentUser();

        if ( $user->getId() ) {
            return $this->redirect('users/cabinet');
        }

        // вход в систему
        $form = $model->getLoginForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                if ( $auth->login( $form->login, $form->password ) ) {
                    return $this->redirect($this->request->getRequest()->server->get('HTTP_REFERER'));
                } else {
                    $this->getTpl()->assign('error',1);
                    $this->getTpl()->assign('msg',$auth->getMessage());
                }
            }
        }
        $this->request->setTitle(t('user','Sign in site'));
        $this->tpl->assign('form', $form );

        return $this->tpl->fetch('users.login');
    }

    /**
     * Редактирование профиля пользователя
     * @return mixed
     */
    public function editAction()
    {
        /** @var UserModel $model */
        $model  = $this->getModel('user');
        //$this->request->set('tpldata.page.name', 'Edit Profile');
        $this->request->setTitle(t('user','Edit profile'));
        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece('users/cabinet',t('user','User cabiner'))
            ->addPiece(null,t('user','Edit profile'));

        $form = $model->getProfileForm();

        $form->setData( $this->app()->getAuth()->currentUser()->getAttributes() );

        // сохранение профиля
        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $user   = $model->find( $form->getField('id')->getValue() );
                if ( $user ) {
                    $user->attributes =  $form->getData();
                    if ( $model->save( $user ) ) {
                        return t('Data save successfully');
                    } else {
                        return t('Data not saved');
                    }
                }
            } else {
                if ( $this->request->isAjax() ) {
                    return array('error'=>1,'errors'=>$form->getErrors());
                }
            }
        };

        return array('form' => $form );
    }

    /**
     * Рагистрация пользователя
     * @return mixed
     */
    public function registerAction()
    {
        $this->request->setTitle(t('user','Join'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece('users/login',t('user','Sign in site'))
            ->addPiece(null,$this->request->getTitle());

        /**
         * @var UserModel $model
         */
        $model  = $this->getModel('User');

        $form = $model->getRegisterForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $user   = $model->createObject();
                $user->attributes = $form->getData();
                $auth   = $this->app()->getAuth();
                $auth->register( $user );
                if ( $auth->getError() ) {
                    $this->request->addFeedback($auth->getMessage());
                } else {
                    return $this->tpl->fetch('users.register_successfull');
                }
            } else {
                $this->request->addFeedback($form->getFeedbackString());
            }
        }
        return $form->html();
    }

    /**
     * Сюда приходит пользователь по ссылке восстановления пароля
     * @param string $email
     * @param string $code
     */
    public function recoveryAction( $email, $code )
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle(t('user','Password recovery'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Main'))
            ->addPiece('users/login',t('user','Sign in site'))
            ->addPiece(null,$this->request->getTitle());

        if ( $email && $code ) {

            $model = $this->getModel('User');
            // проверка, подходят ли email и code
            /** @var $user Data_Object_User */
            $user  = $model->find(array(
                'cond'  => 'email = :email',
                'params'=> array(':email'=>$email),
            ));

            if ( $user ) {
                if ( $code == md5( $user->solt ) ) {
                    $pass = $this->app()->getAuth()->generateString( 8 );
                    $user->changePassword( $pass );

                    $this->tpl->assign(array(
                        'pass'      => $pass,
                        'login'     => $user['login'],
                        'sitename'  => $this->config->get('sitename'),
                        'loginform' => $this->config->get('siteurl').
                                $this->router->createLink("users/login")
                    ));

                    sendmail(
                        $this->config->get('admin'),
                        $email,
                        t('user','New password'), $this->tpl->fetch('users.mail.recovery')
                    );
                    return array('error' => 0, 'msg' => t('user','A new password has been sent to your e-mail'));
                } else {
                    return array('error' => 1, 'msg' => t('user','Incorrect recovery code'));
                }
            } else {
                return array('error' => 1, 'msg' => t('user','Your email is not found'));
            }
        }
        return array('error'=>1,'msg' => t('user','Not specified recovery options'));
    }

    /**
     * Восстановление пароля
     * @return array
     */
    public function restoreAction()
    {
        /**
         * @var Data_Object_User $user
         * @var UserModel $model
         */
        // @TODO Перевести под новую модель

        $this->request->setTemplate('inner');
        $this->request->setTitle(t('user','Password recovery'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Main'))
            ->addPiece('users/login',t('user','Sign in site'))
            ->addPiece(null,$this->request->getTitle());

        // 1. Если нет параметров, форма ввода email
        // 2. Если введен email, отправить письмо, где будет ссылка, с email и md5 соли
        // 3. Если email и md5 соли подходят, то выслать новый пароль
        // $model  = $this->getModel('User');

        $form = new FormRestore();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $model = $this->getModel('User');
                $user  = $model->find(array(
                    'cond'  => 'email = :email',
                    'params'=> array(':email'=>$form->email),
                ));

                if ( $user ) {
                    $this->tpl->assign(array(
                        'login'     => $user['login'],
                        'sitename'  => $this->config->get('sitename'),
                        'siteurl'   => $this->config->get('siteurl'),
                        'link'      => $this->config->get('siteurl')
                                      . $this->router->createServiceLink(
                                            "users", "recovery",
                                            array('email'=>$user['email'], 'code'=>md5($user['solt']),  )
                                        )
                    ));
                    sendmail(
                        $this->config->get('admin'),
                        $form->email,
                        t('user','Password recovery'),
                        $this->tpl->fetch('users.mail.restore')
                    );
                    return array('success'=>1,'msg'=>t('user','Recovery link sent to your e-mail'));
                } else {
                    $this->request->addFeedback(t('user','The user with the mailbox is not registered'));
                }
            }
            $this->request->addFeedback( $form->getFeedbackString() );
        }
        return array('form'=>$form);
    }

    /**
     * Изменение пароля
     * @return mixed
     */
    public function passwordAction()
    {
        /**
         * @var Form_Form $form
         * @var UserModel $model
         */
        // @TODO Перевести под новую модель
        $this->request->setTitle(t('user','Change password'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece('users/cabinet',t('user','User cabiner'))
            ->addPiece(null, $this->request->getTitle());


        $model  = $this->getModel('User');
        $auth   = $this->app()->getAuth();
        $user   = $auth->currentUser();

        $form = $model->getPasswordForm();

        //printVar($this->user->getData());

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $pass_hash  = $auth->generatePasswordHash(
                    $form->getField('password')->getValue(), $user->get('solt')
                );
                //$pass_hash = $this->user->generatePasswordHash( $form->password, $this->user->get('solt') );

                if ( $user->get('password') == $pass_hash )
                {
                    //$this->request->addFeedback('Пароль введен верно');

                    if ( strcmp( $form->getField('password1')->getValue(), $form->getField('password2')->getValue() ) === 0 ) {
                        $user->changePassword( $form->getField('password1')->getValue() );
                        $this->request->addFeedback(t('user','Password successfully updated'));
                        return $this->tpl->fetch('system:users.password_success');
                    }
                    else {
                        $this->request->addFeedback(t('user','You must enter a new password 2 times'));
                    }

                } else {
                    $this->request->addFeedback(t('user','Password is not correct'));
                }

            } else {
                $this->request->addFeedback( $form->getFeedbackString() );
            }
        }

        $this->tpl->assign(array(
            'form'  => $form
        ));

        return $this->tpl->fetch('users.password');
    }

}
