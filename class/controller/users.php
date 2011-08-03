<?php
class Controller_Users extends Controller
{
    function init()
    {
        $this->request->set('template', 'inner' );
    }

    function access()
    {
        return array(
            'system'    => array('admin','adminEdit'),
        );
    }

    public function indexAction()
    {
        $auth   = $this->app()->getAuth();
        if ( $this->request->get('confirm') && $this->request->get('userid', FILTER_VALIDATE_INT) )
        { // подтверждение регистрации
            $auth->confirm();
            $this->request->addFeedback($auth->getMessage());
        }

        $this->loginAction();
    }

    /**
     * Управление пользователем
     * @return void
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

        $this->request->setContent( $this->tpl->fetch('users.admin') );
    }

    /**
     * Редактирование пользователя в админке
     * @return void
     */
    public function adminEditAction()
    {
        /**
         * @var model_User $model
         * @var Data_Object_User $user
         * @var form_Form $user_form
         */
        $model  = $this->getModel('user');

        $user_form = $model->getEditForm();

        $user_id = $this->request->get('userid');

        if ( $user_form->getPost() )
        {
            $this->setAjax();

            if ( $user_form->validate() )
            {
                if ( $user_id = $user_form['id'] ) {
                    $user   = $model->find( $user_id );
                    $user->setAttributes( $user_form->getData() );
                } else {
                    $user = $model->createObject( $user_form->getData() );
                }

                if ( $user_form['password'] ) {
                    $user->changePassword( $user_form['password'] );
                }
                else {
                    unset( $user['password'] );
                }

                if ( $ins = $model->save( $user ) )
                {
                    if ( ! $user_id )
                    {
                        // если создан новый пользователь
                        print "Пользователь добавлен";
                        reload( '/admin/users/edit/'.$ins );
                    }
                    $this->request->addFeedback('Данные сохранены');
                } else {
                    $this->request->addFeedback('Данные не были сохранены');
                }
            }
            else {
                $this->request->addFeedback('Форма заполнена не правильно');
            }
            return;
        }

        if ( $user_id && $user_data = $model->find( $user_id ) )
        {
            $user_form->setData( $user_data->getAttributes() );
            $user_form->getField('password')->setValue('');
        }


        $this->tpl->user_form   = $user_form->html();
        $content = $this->tpl->fetch('system:users.edit');


        $this->request->set('template', 'index');
        $this->request->setTitle((!$user_id ? 'Добавить пользователя' : 'Правка пользователя'));
        $this->request->setContent( $content );
    }

    /**
     * Выход
     */
    public function logoutAction()
    {
        $this->app()->getAuth()->logout();
        redirect('users/login');
    }

    /**
     * Вход
     */
    public function loginAction()
    {
        /**
         * @var Model_User $model
         */
        $model  = $this->getModel('User');
        $auth   = $this->app()->getAuth();

        $user   = $auth->currentUser();

        if ( $user->getId() ) {
            return $this->cabinetAction();
        }

        $this->request->setTitle(t('Personal page'));

        if ( ! $user->getId() ) {
            // вход в систему
            $form = $model->getLoginForm();

            if ( $form->getPost() ) {
                if ( $form->validate() ) {
                    //print "login: {$form->login} pass:{$form->password}";
                    if ( $auth->login( $form->login, $form->password ) ) {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    else {
                        $this->request->addFeedback( $auth->getMessage() );
                    }
                }
            }
            $this->request->setTitle('Вход в систему');

            $this->tpl->assign('form', $form );
        }

        $this->request->setContent($this->tpl->fetch('users.cabinet'));
    }

    /**
     * @return void
     */
    public function cabinetAction()
    {
        $auth   = $this->app()->getAuth();
        $user   = $auth->currentUser();

        if ( $user->getId() ) {
            // отображаем кабинет
            $this->tpl->user    = $user->getAttributes();
            $this->request->setTitle('Кабинет пользователя');

            $this->request->setContent($this->tpl->fetch('users.cabinet'));
        }
        else {
            reload('users/login');
        }
    }

    /**
     * Редактирование профиля пользователя
     * @return void
     */
    public function editAction()
    {
        $model  = $this->getModel('user');

        $this->request->set('tpldata.page.name', 'Edit Profile');
        $this->request->setTitle('Редактирование профиля');
        //$this->request->set('tpldata.page.template', 'index');

        $form = $model->getProfileForm();

        $form->setData( $this->app()->getAuth()->currentUser()->getAttributes() );

        // сохранение профиля
        if ( $form->getPost() && $form->validate() ) {
            $user   = $model->createObject( $form->getData() );

            if ( $model->save( $user ) ) {
                $this->request->addFeedback('Профиль успешно сохранен');
            }
            else {
                $this->request->addFeedback('Профиль не сохранен');
            }
        }

        $this->tpl->assign('form', $form);

        $this->request->setContent($this->tpl->fetch('system:users.profile'));
    }

    /**
     * Рагистрация пользователя
     * @return void
     */
    public function registerAction()
    {
        $this->request->set('tpldata.page.name', 'Register');
        $this->request->setTitle('Регистрация');
        $this->request->setContent('');

        /**
         * @var Model_User $model
         */
        $model  = $this->getModel('User');

        $form = $model->getRegisterForm();

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $user   = $model->createObject( $form->getData() );

                $auth   = $this->app()->getAuth();

                $auth->register( $user );

                if ( $auth->getError() )
                {
                    $this->request->addFeedback($auth->getMessage());
                } else {
                    $this->request->setContent($this->tpl->fetch('users.register_successfull'));
                    return;
                }
            }
            else {
                $this->request->addFeedback('Форма заполнена не верно');
            }
        }
        $this->request->setContent($form->html());
    }

    /**
     * Восстановление пароля
     * @return void
     */
    public function restoreAction()
    {
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

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $user  = $model->find(array(
                    'cond'  => 'email = :email',
                    'params'=> array(':email'=>$form['email']),
                ));

                if ( $user )
                {
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
                }
                else {
                    $this->request->addFeedback('Пользователь с этим почтовым ящиком не зарегистрирован');
                }
            }
        }

        $this->request->setContent('<p>Для восстановления пароля укажите ваш адрес Email</p>'.$form->html());
    }

    /**
     * Изменение пароля
     * @return void
     */
    public function passwordAction()
    {
        // @T ODO Перевести под новую модель
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
                $pass_hash  = $auth->generatePasswordHash( $form->password, $user->solt );
                //$pass_hash = $this->user->generatePasswordHash( $form->password, $this->user->get('solt') );

                if ( $user->password == $pass_hash )
                {
                    //$this->request->addFeedback('Пароль введен верно');

                    if ( strcmp( $form->password1, $form->password2 ) === 0 )
                    {
                        $user->changePassword( $form->password1 );
                        $this->request->addFeedback('Пароль успешно изменен');
                        $this->request->setContent($this->tpl->fetch('system:users.password_success'));
                        return;
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

        $this->request->setContent( $this->tpl->fetch('users.password') );
    }

}







