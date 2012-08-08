<?php
/**
 * Контроллер управления пользователями
 */
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
            'system'    => array('admin','adminEdit','save'),
        );
    }

    /**
     * Основное действие
     * Выводит форму логина либо подтверждает регистрацию
     * @return mixed
     */
    public function indexAction()
    {
        $auth   = $this->app()->getAuth();
        // подтверждение регистрации
        if ( $this->request->get('confirm') && $this->request->get('userid', FILTER_VALIDATE_INT) ) {
            $auth->confirm();
            $this->request->addFeedback($auth->getMessage());
        }

        return $this->loginAction();
    }

    /**
     * Управление пользователем
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->set('template', 'index' );

        // используем шаблон админки
        $this->request->setTitle('Пользователи');

        if ( $this->request->get('userid') || $this->request->get('add') ) {
            return $this->adminEditAction();
        }

        $model  = $this->getModel('user');

        $users = $this->request->get('users');

        if ( $users ) {
            foreach( $users as $key => $user ) {
                if ( isset( $user['delete'] ) ) {
                    $model->delete($key);
                    $this->request->addFeedback("Удален пользователь № {$key}");
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
                $this->request->addFeedback('Слишком короткий запрос');
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
     * @return mixed
     */
    public function adminEditAction()
    {
        /**
         * @var model_User $model
         * @var Data_Object_User $user
         * @var form_Form $userForm
         */
        $model  = $this->getModel('user');

        $userForm = $model->getEditForm();

        $user_id = $this->request->get('userid');

        if ( $user_id && $User = $model->find( $user_id ) )
        {
            $userForm->setData( $User->getAttributes() );
            $userForm->getField('password')->setValue('');
        }

        $this->request->set('template', 'index');
        $this->request->setTitle((!$user_id ? 'Добавить пользователя' : 'Правка пользователя'));

        $this->tpl->assign('form', $userForm);
        $this->tpl->assign('title', $this->request->getTitle());
        return $this->tpl->fetch('system:users.adminedit');
    }


    /**
     * @return mixed
     */
    public function saveAction()
    {
        /**
         * @var Model_User $model
         * @var Data_Object_User $User
         * @var Form_Form $userForm
         */
        $model  = $this->getModel('User');

        $userForm = $model->getEditForm();

        if ( $userForm->getPost() ) {

            if ( $userForm->validate() ) {
                if ( $user_id = $userForm['id'] ) {
                    $User   = $model->find( $user_id );
                    $User->setAttributes( $userForm->getData() );
                } else {
                    $User = $model->createObject( $userForm->getData() );
                }

                if ( $userForm['password'] ) {
                    $User->changePassword( $userForm['password'] );
                } else {
                    unset( $User['password'] );
                }

                if ( ! $user_id ) {
                    // если создан новый пользователь
                    $ins = $User->save();
                    $this->reload( '/users/admin/edit/'.$ins );
                    print "Пользователь добавлен";
                }
                $User->markDirty();
                return t('Data save successfully');
            }
            return $this->request->getFeedbackString();
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
        /** @var Model_User $model */
        $model  = $this->getModel('User');
        $auth   = $this->app()->getAuth();

        $user   = $auth->currentUser();

        if ( $user->getId() ) {
//            return $this->cabinetAction();
            return $this->redirect('users/cabinet');
        }

        // вход в систему
        $form = $model->getLoginForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                if ( $auth->login( $form->getField('login')->getValue(), $form->getField('password')->getValue() ) ) {
                    return $this->redirect($_SERVER['HTTP_REFERER']);
                } else {
                    return $auth->getMessage();
                }
            }
        }
        $this->request->setTitle('Вход в систему');
        $this->tpl->assign('form', $form );

        return $this->tpl->fetch('users.cabinet');
    }

    /**
     * @return mixed
     */
    public function cabinetAction()
    {
        $auth   = $this->app()->getAuth();
        $user   = $auth->currentUser();

        if ( $user->getId() ) {
            // отображаем кабинет
            $this->tpl->assign('user', $user->getAttributes());
            $this->request->setTitle('Кабинет пользователя');
        } else {
            $this->reload('users/login');
        }
    }

    /**
     * Редактирование профиля пользователя
     * @return mixed
     */
    public function editAction()
    {
        /** @var Model_User $model */
        $model  = $this->getModel('user');

        $this->request->set('tpldata.page.name', 'Edit Profile');
        $this->request->setTitle('Редактирование профиля');

        $form = $model->getProfileForm();

        $form->setData( $this->app()->getAuth()->currentUser()->getAttributes() );

        // сохранение профиля
        if ( $form->getPost() && $form->validate() ) {
            $user   = $model->createObject( $form->getData() );

            if ( $model->save( $user ) ) {
                return 'Профиль успешно сохранен';
            } else {
                return 'Профиль не сохранен';
            }
        }

        $this->tpl->assign('form', $form);

        return $this->tpl->fetch('system:users.profile');
    }

    /**
     * Рагистрация пользователя
     * @return mixed
     */
    public function registerAction()
    {
        $this->request->setTitle('Регистрация');
        $this->request->set('tpldata.page.name', 'Register');
        $this->request->setContent('');

        /**
         * @var Model_User $model
         */
        $model  = $this->getModel('User');

        $form = $model->getRegisterForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $user   = $model->createObject( $form->getData() );
                $auth   = $this->app()->getAuth();
                $auth->register( $user );
                if ( $auth->getError() ) {
                    $this->request->addFeedback($auth->getMessage());
                } else {
                    return $this->tpl->fetch('users.register_successfull');
                }
            } else {
                $this->request->addFeedback('Форма заполнена не верно');
            }
        }
        return $form->html();
    }

    /**
     * Восстановление пароля
     * @return mixed
     */
    public function restoreAction()
    {
        /**
         * @var Data_Object_User $user
         * @var Model_User $model
         */
        // @TODO Перевести под новую модель
        $this->request->set('tpldata.page.name', 'Restore');
        $this->request->setTemplate('inner');
        $this->request->setTitle('Восстановление пароля');
        $this->request->setContent('');


        // 1. Если нет параметров, форма ввода email
        // 2. Если введен email, отправить письмо, где будет ссылка, с email и md5 соли
        // 3. Если email и md5 соли подходят, то выслать новый пароль

        $email = $this->request->get('email');
        $code  = $this->request->get('code');

        $model  = $this->getModel('User');


        if ( $email && $code ) {
            // проверка, подходят ли email и code
            $user  = $model->find(array(
                'cond'  => 'email = :email',
                'params'=> array(':email'=>$email),
            ));

            if ( $user )
            {
                if ( $code == md5( $user['solt'] ) )
                {
                    $pass = $this->app()->getAuth()->generateString( 8 );
                    $user->changePassword( $pass );

                    //$model->save( $user );

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
                        'Новый пароль', $this->tpl->fetch('users.password_new')
                    );
                    $this->request->addFeedback('Новый пароль отправлен на Вашу почту');
                    return;
                }
                else {
                    $this->request->addFeedback('Не верный код восстановления');
                }
            }
            else {
                $this->request->addFeedback('Ваш email не найден');
            }
        }

        $form = new form_Form(array(
            'name'  => 'restore',
            'fields'=> array(
                'email' => array('type'=>'text', 'label'=>'Ваш Email адрес',),
                'submit'=> array('type'=>'submit', 'value'=>'Запросить'),
            ),
        ));

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $user  = $model->find(array(
                    'cond'  => 'email = :email',
                    'params'=> array(':email'=>$form['email']),
                ));

                if ( $user ) {
                    $this->tpl->assign(array(
                        'login'     => $user['login'],
                        'sitename'  => $this->config->get('sitename'),
                        'siteurl'   => $this->config->get('siteurl'),
                        'link'      => $this->config->get('siteurl').$this->router->createLink(
                            "users/restore",
                            array('email'=>$user['email'], 'code'=>md5($user['solt']),  )
                        )
                    ));
                    sendmail(
                        $this->config->get('admin'),
                        $form['email'],
                        'Восстановление пароля',
                        $this->tpl->fetch('users.password_restore')
                    );
                    $this->request->addFeedback('Ссылка для восстановления отправлена на вашу почту');
                    return;
                } else {
                    $this->request->addFeedback('Пользователь с этим почтовым ящиком не зарегистрирован');
                }
            }
        }

        return '<p>Для восстановления пароля укажите ваш адрес Email</p>'.$form->html();
    }

    /**
     * Изменение пароля
     * @return mixed
     */
    public function passwordAction()
    {
        /**
         * @var Form_Form $form
         * @var Model_User $model
         */
        // @TODO Перевести под новую модель
        $this->request->setTitle('Изменить пароль');

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
                        $this->request->addFeedback('Пароль успешно изменен');
                        return $this->tpl->fetch('system:users.password_success');
                    }
                    else {
                        $this->request->addFeedback('Нужно ввести новый пароль 2 раза');
                    }

                } else {
                    $this->request->addFeedback('Пароль введен не верно');
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
