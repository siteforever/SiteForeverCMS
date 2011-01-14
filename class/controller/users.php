<?php
class controller_Users extends Controller
{
    function init()
    {
        $this->request->set('template', 'inner' );
    }

    function indexAction()
    {
        $model  = $this->getModel('User');

        if ( $this->request->get('confirm') && $this->request->get('userid', FILTER_VALIDATE_INT) )
        { // подтверждение регистрации
           $this->app()->getAuth()->confirm();
        }

        $option = $this->request->get('option');
        if ( $option == 'whole_request' ) {
            sendmail(
               $this->user->email,
               $this->config->get('admin'),
               'Запрос на оптовый аккаунт',
               'Пользователь '.$this->user->login.
               ' с id: '.$this->user->getId().
               ', email: '.$this->user->email.
               ' запросил перевести его аккаунт в статус "оптовый".'
           );
           $this->request->addFeedback('Ваша заявка принята');
        }

        $this->loginAction();
    }

    /**
     * Управление пользователем
     * @return void
     */
    function adminAction()
    {
        $this->request->set('template', 'index' );
        // используем шаблон админки
        $this->request->setTitle('Пользователи');

        $model  = $this->getModel('user');

        $users = $this->request->get('users');

        if ( $users ) {
            foreach( $users as $key => $user ) {

                foreach( $user as $k => $v ) {
                    $user[$k]  = trim($v);
                }

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
        );
        if ( $search ) {
            if ( strlen($search) > 2 ) {
                $search = '%'.$search.'%';
                $criteria['cond']   =   ' login LIKE :search OR email LIKE :search '.
                                        ' OR lname LIKE :search OR name LIKE :search ';
            } else {
                $this->request->addFeedback('Слишком короткий запрос');
            }
        }

        $count  = $model->count( $criteria['cond'], array(':search'=>$search) );

        $paging = $this->paging($count, 25, '/admin/users/page%page%');

        $criteria['limit']  = $paging['offset'].', '.$paging['perpage'];
        $criteria['order']  = 'login';

        $users = $model->findAll($criteria);

        $this->tpl->assign('users', $users);
        $this->tpl->assign('paging', $paging);
        $this->tpl->assign('groups', $this->config->get('users.groups'));

        $this->request->setContent( $this->tpl->fetch('users.admin'));
    }

    /**
     * Редактирование пользователя в админке
     * @return void
     */
    function adminEditAction()
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
                $user = $model->createObject( $user_form->getData() );

                if ( (string) $user_form->password ) {
                    $user->changePassword( (string) $user_form->password );
                }
                else {
                    unset( $user->password );
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
    function logoutAction()
    {
        $this->app()->getAuth()->logout();
        redirect('users/login');
    }

    /**
     * Вход
     */
    function loginAction()
    {
        $model  = $this->getModel('user');

        $this->request->setTitle('tpldata.page.name', t('Personal page'));

        if ( ! $this->user->id ) {
            // вход в систему
            $form = $model->getLoginForm();

            if ( $form->getPost() ) {
                if ( $form->validate() ) {
                    if ( $this->app()->getAuth()->login( $form->login, $form->password ) ) {
                        redirect();
                    }
                }
            }
            $this->request->setTitle('Вход в систему');

            $this->tpl->assign('form', $form );
        }
        else {
            // отображаем кабинет
            $this->tpl->user    = $this->user->getAttributes();
            $this->request->setTitle('Кабинет пользователя');
        }

        $this->request->setContent($this->tpl->fetch('users.cabinet'));
    }

    /**
     * Редактирование профиля пользователя
     * @return void
     */
    function editAction()
    {
        $model  = $this->getModel('user');

        $this->request->set('tpldata.page.name', 'Edit Profile');
        $this->request->setTitle('Редактирование профиля');
        //$this->request->set('tpldata.page.template', 'index');

        $form = $model->getProfileForm();

        $form->setData( App::$user->getAttributes() );

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
    function registerAction()
    {
        $this->request->set('tpldata.page.name', 'Register');
        $this->request->setTitle('Регистрация');
        $this->request->setContent('');

        $model  = $this->getModel('user');

        $form = $model->getRegisterForm();

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $user   = $model->createObject( $form->getData() );

                if ( $model->register( $user ) )
                {
                    return 1;
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
    function restoreAction()
    {
        // @TODO Перевести под новую модель
        $this->request->set('tpldata.page.name', 'Restore');
        $this->request->set('tpldata.page.template', 'index');
        $this->request->setTitle('Восстановление пароля');
        $this->request->setContent('');


        // 1. Если нет параметров, фома ввода email
        // 2. Если введен email, отправить письмо, где будет ссылка, с email и md5 соли
        // 3. Если email и md5 соли подходят, то выслать новый пароль

        $email = $this->request->get('email');
        $code  = $this->request->get('code');

        $user = $this->user;


        if ( $email && $code ) {
            // проверка, подходят ли email и code
            $data = $user->findByEmail( $email );

            if ( $data )
            {
                if ( $code == md5( $data['solt'] ) )
                {
                    $pass = $user->generateString( 8 );
                    $user->changePassword( $pass );
                    $user->update();

                    $this->tpl->assign(array(
                        'pass'  => $pass,
                        'login'  => $data['login'],
                        'sitename'  => $this->config->get('sitename'),
                        'loginform' => $this->config->get('siteurl').
                                $this->router->createLink("users/login")
                    ));

                    sendmail(
                        $this->config->get('admin'),
                        $email,
                        'Новый пароль', $this->tpl->fetch('db:mail_new_password')
                    );
                    $this->request->addFeedback('Новый пароль отправлен на Вашу почту.');
                    return 1;
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
                $data = $user->findByEmail($form->email);

                if ( $data )
                {
                    $this->tpl->assign(array(
                        'sitename'  => $this->config->get('sitename'),
                        'siteurl'   => $this->config->get('siteurl'),
                        'link'      => $this->config->get('siteurl').$this->router->createLink(
                            "users/restore",
                            array('email'=>$data['email'], 'code'=>md5($data['solt']),  )
                        )
                    ));
                    sendmail(
                        $this->config->get('admin'),
                        $form->email,
                        'Восстановление пароля',
                        $this->tpl->fetch('db:mail_restore')
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
    function passwordAction()
    {
        // @TODO Перевести под новую модель
        $this->request->setTitle('Изменить пароль');

        $form = $this->user->getPasswordForm();

        //printVar($this->user->getData());

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $pass_hash = $this->user->generatePasswordHash( $form->password, $this->user->get('solt') );

                if ( $this->user->get('password') == $pass_hash )
                {
                    //$this->request->addFeedback('Пароль введен верно');

                    if ( strcmp($form->password1,$form->password2) == 0 )
                    {
                        $this->user->changePassword( $form->password1 );
                        $this->user->update();

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

        $this->request->setContent($this->tpl->fetch('users.password'));
    }

}







