<?php
/**
 * Контроллер управления пользователями
 */
namespace Module\User\Controller;

use Module\User\Object\User;
use Sfcms\Controller;
use Forms\User\Restore as FormRestore;
use Module\User\Model\UserModel;
use Sfcms\Form\Form;
use Sfcms_Http_Exception;
use Symfony\Component\Security\Core\Util\SecureRandom;

class UserController extends Controller
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
            USER_USER   => array('edit', 'cabinet', 'password'),
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
    public function indexAction($confirm, $userid)
    {
        // подтверждение регистрации
        if ($confirm && $userid) {
            $this->request->setTitle('Подтверждение регистрации');
            $this->getTpl()->getBreadcrumbs()->addPiece('index', $this->t('Home'))->addPiece(
                    'user',
                $this->t('user', 'Sign in site')
                )->addPiece(null, $this->request->getTitle());

            /** @var User $user */
            $user = $this->getModel('User')->find( array(
                    'cond'      => 'id = :id AND confirm = :confirm',
                    'params'    => array(':id'=>$userid, ':confirm'=>$confirm),
                ));

            if ($user) {
                $user->perm = USER_USER;
                $user->last = time();
                $user->active();
                $user->confirm = md5(microtime() . $user->solt);

                return array('error'=>false, 'success' => 1, 'message'=>$this->t('Регистрация успешно подтверждена'));
            } else {
                return array('error'=>true, 'message'=>'Ваш аккаунт не подвержден, обратитесь к '
                . '<a href="mailto:'.$this->config->get('admin').'">администрации сайта</a>'
                );
            }
        }

        return $this->redirect('user/login');
    }


    /**
     * @return array
     * @throws Sfcms_Http_Exception
     */
    public function cabinetAction()
    {
        $this->tpl->getBreadcrumbs()->addPiece('index',$this->t('Home'))->addPiece(null,$this->t('user','User cabiner'));

        if ( $this->auth->getId() ) {
            // отображаем кабинет
            $this->request->setTitle($this->t('user','User cabiner'));
            return array( 'user' => $this->auth->currentUser());
        }
        throw new Sfcms_Http_Exception($this->t('Access denied'), 403);
    }


    /**
     * Управление пользователем
     * @return mixed
     */
    public function adminAction()
    {
        $this->request->set('template', 'index' );

        // используем шаблон админки
        $this->request->setTitle($this->t('user','Users'));

        if ($id = $this->request->get('id')) {
            return $this->adminEdit((int) $id);
        }

        $model  = $this->getModel('User');

        $users = $this->request->get('users');

        if ($users) {
            foreach( $users as $key => $user ) {
                if ( isset( $user['delete'] ) ) {
                    $model->delete($key);
                    $this->request->addFeedback($this->t('user','Deleted user #').$key);
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
                $this->request->addFeedback($this->t('user','Too short query'));
            }
        }

        $count  = $model->count( $criteria['cond'], $criteria['params'] );

        $paging = $this->paging($count, 25, 'user/admin');

        $criteria['limit']  = $paging['offset'].', '.$paging['perpage'];
        $criteria['order']  = 'login';

        $users = $model->findAll($criteria);

        $this->tpl->assign('users', $users);
        $this->tpl->assign('paging', $paging);
        $this->tpl->assign('request', $this->request);
        $this->tpl->assign('groups', $this->config->get('users.groups'));

        return $this->tpl->fetch('user.admin');
    }

    /**
     * Редактирование пользователя в админке
     * @param int $id
     * @return array
     */
    public function adminEdit($id)
    {
        /**
         * @var UserModel $model
         * @var User $user
         * @var Form $userForm
         */
        $model  = $this->getModel('User');

        $userForm = $model->getEditForm();

        if ( $id && $User = $model->find( $id ) )
        {
            $userForm->setData( $User->getAttributes() );
            $userForm->getField('password')->setValue('');
        }

        $this->request->set('template', 'index');
        $this->request->setTitle((!$id ? $this->t('user','Add user') : $this->t('user','Edit user') ));

        return $this->render('user.adminedit', array(
            'form' => $userForm,
        ));
    }


    /**
     * @return mixed
     */
    public function saveAction()
    {
        /**
         * @var UserModel $model
         * @var User $User
         * @var Form $userForm
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
                return array('error'=>0,'msg'=>$this->t('Data save successfully'));
            } else {
                return array('error'=>1,'msg'=>$userForm->getFeedbackString());
            }
        }
        return $this->t('Data not sent');
    }

    /**
     * Выход
     */
    public function logoutAction()
    {
        $this->app->getAuth()->logout();
        return $this->redirect('user/login');
    }

    /**
     * Вход
     * @return mixed
     */
    public function loginAction()
    {
        $this->request->setTitle($this->t('Personal page'));
        /** @var UserModel $model */
        $model  = $this->getModel('User');

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece(null, $this->t('user','Sign in site'));

        if ($this->auth->getId()) {
            return $this->redirect('user/cabinet');
        }

        // вход в систему
        $form = $model->getLoginForm();

        if ($form->getPost()) {
            if ($form->validate()) {
                $result = $this->login($form->login, $form->password);
                if (!$result['error']) {
                    return $this->redirect($this->request->server->get('HTTP_REFERER'));
                } else {

                    $this->getTpl()->assign($result);
                }
            }
        }
        $this->request->setTitle($this->t('user','Sign in site'));
        $this->tpl->assign('form', $form );

        return $this->render('user.login');
    }

    /**
     * Логин
     * @param string $login
     * @param string $password
     */
    private function login( $login, $password )
    {
        if ( $password == '' ) {
            return array('error'=>1, 'message'=>$this->t('user','Empty password'));
        }

        /** @var User $user */
        $user = $this->getModel('User')->find(array(
            'cond' => 'login = ?',
            'params'=> array($login),
        ));

        if ($user) {
            if ($user->status == 0) {
                return array('error' => 1, 'message' => $this->t('user', 'Your account has been disabled'));
            }
            if ($user->perm < USER_USER) {
                return array('error' => 1, 'message' => $this->t('user', 'Not enough permissions'));
            }

            $password = $user->generatePasswordHash($password, $user->solt);

            if ($password != $user->password) {
                return array('error' => 1, 'message' => $this->t('user', 'Your password is not suitable'));
            }

            $user->last = time();
            $this->auth->setId($user->id);

//            if ( $user->perm == USER_ADMIN ) {
                // Авторизация Sypex Dumper
//                $this->app()->getSession()->sxd_auth    = 1;
//                $this->app()->getSession()->sxd_conf    = $this->app()->getConfig()->get('db');
//            }

            return array('error' => 0, 'message'=>$this->t('user','Authorization was successful'));
        }

        return array('error' => 1, 'message'=>$this->t('user','Your login is not registered'));
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
        $this->request->setTitle($this->t('user','Edit profile'));
        $this->tpl->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('user/cabinet',$this->t('user','User cabiner'))
            ->addPiece(null,$this->t('user','Edit profile'));

        $form = $model->getProfileForm();

        $form->setData( $this->auth->currentUser()->getAttributes() );

        // сохранение профиля
        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $user   = $model->find( $form->getField('id')->getValue() );
                if ( $user ) {
                    $user->attributes =  $form->getData();
                    if ( $model->save( $user ) ) {
                        return $this->t('Data save successfully');
                    } else {
                        return $this->t('Data not saved');
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
        $this->request->setTitle($this->t('user','Join'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('user/login',$this->t('user','Sign in site'))
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
                if ($this->register( $user )) {
                    return $this->tpl->fetch('user.register_successfull');
                }
            } else {
                $this->request->addFeedback($form->getFeedbackString());
            }
        }
        return $form->html();
    }


    /**
     * Регистрация
     * @param User $obj
     */
    public function register( User $obj )
    {
        if ( strlen( $obj['login'] ) < 5 ) {
            $this->request->addFeedback('Логин должен быть не короче 5 символов');
            return false;
        }

        if ( strlen( $obj['password'] ) < 6 ) {
            $this->request->addFeedback( 'Пароль должен быть не короче 6 символов' );
            return false;
        }

        $obj['solt']    = $obj->generateString(8);
        $obj['password']= $obj->generatePasswordHash($obj->password, $obj->solt);
        $obj['perm']    = USER_GUEST;
        $obj['status']  = 0;
        $obj['confirm'] = md5($obj['solt'] . md5(microtime(1) . $obj['solt']));

        $obj['date']   = time();
        $obj['last']   = time();

        $model = $obj->getModel();

        $user   = $model->find(array(
                'cond'     => 'login = :login',
                'params'   => array(':login'=>$obj['login']),
            ));

        if ( $user ) {
            $this->request->addFeedback( 'Пользователь с таким логином уже существует' );
            return false;
        }

        $user   = $model->find(array(
                'cond'     => 'email = :email',
                'params'   => array(':email'=>$obj['email']),
            ));

        if ( $user ) {
            $this->request->addFeedback( 'Пользователь с таким адресом уже существует' );
            return false;
        }

        //printVar($obj->getAttributes());

        //return;

        //$this->setData( $data );
        // Надо сохранить, чтобы знать id
        if ( $model->save( $obj ) ) {
            $tpl    = $this->getTpl();
            $config = $this->config;

            $tpl->user = $obj;
            $tpl->sitename = $config->get('sitename');
            $tpl->siteurl  = $config->get('siteurl');

            $msg = $tpl->fetch('user.mail.register');

            //print $msg;

            $this->sendmail(
                $config->get('admin'),
                $obj->email,
                'Подтверждение регистрации',
                $msg
            );

            $this->request->addFeedback("Регистрация прошла успешно. ".
                "На Ваш Email отправлена ссылка для подтверждения регистрации.");
            return true;
        }
        return false;
    }


    /**
     * Сюда приходит пользователь по ссылке восстановления пароля
     * @param string $email
     * @param string $code
     *
     * @return array
     */
    public function recoveryAction($email, $code)
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle($this->t('user','Password recovery'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',$this->t('Main'))
            ->addPiece('user/login',$this->t('user','Sign in site'))
            ->addPiece(null,$this->request->getTitle());

        if ( $email && $code ) {
            $model = $this->getModel('User');
            // проверка, подходят ли email и code
            /** @var $user User */
            $user  = $model->find(array(
                'cond'  => 'email = :email',
                'params'=> array(':email'=>$email),
            ));

            if ( $user ) {
                if ( $code == md5( $user->solt ) ) {
                    $pass = $user->generateString( 8 );
                    $user->changePassword( $pass );

                    $this->tpl->assign(array(
                        'pass'      => $pass,
                        'login'     => $user['login'],
                        'sitename'  => $this->config->get('sitename'),
                        'loginform' => $this->config->get('siteurl').
                                $this->router->createLink("user/login")
                    ));

                    $this->sendmail(
                        $this->config->get('admin'),
                        $email,
                        $this->t('user','New password'), $this->tpl->fetch('user.mail.recovery')
                    );
                    return array('error' => 0, 'msg' => $this->t('user','A new password has been sent to your e-mail'));
                } else {
                    return array('error' => 1, 'msg' => $this->t('user','Incorrect recovery code'));
                }
            } else {
                return array('error' => 1, 'msg' => $this->t('user','Your email is not found'));
            }
        }
        return array('error'=>1,'msg' => $this->t('user','Not specified recovery options'));
    }

    /**
     * Восстановление пароля
     * @return array
     */
    public function restoreAction()
    {
        /**
         * @var User $user
         * @var UserModel $model
         */
        // @TODO Перевести под новую модель

        $this->request->setTemplate('inner');
        $this->request->setTitle($this->t('user','Password recovery'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',$this->t('Main'))
            ->addPiece('user/login',$this->t('user','Sign in site'))
            ->addPiece(null,$this->request->getTitle());

        // 1. Если нет параметров, форма ввода email
        // 2. Если введен email, отправить письмо, где будет ссылка, с email и md5 соли
        // 3. Если email и md5 соли подходят, то выслать новый пароль
         $model  = $this->getModel('User');

        $form = new FormRestore();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
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
                                            "user", "recovery",
                                            array('email'=>$user['email'], 'code'=>md5($user['solt']),  )
                                        )
                    ));
                    $this->sendmail(
                        $this->config->get('admin'),
                        $form->email,
                        $this->t('user','Password recovery'),
                        $this->tpl->fetch('user.mail.restore')
                    );
                    return array('success'=>1,'msg'=>$this->t('user','Recovery link sent to your e-mail'));
                } else {
                    $this->request->addFeedback($this->t('user','The user with the mailbox is not registered'));
                }
            }
            $this->request->addFeedback( $form->getFeedbackString() );
        }
        return $this->render('user.restore', array('form'=>$form));
    }

    /**
     * Изменение пароля
     * @return mixed
     */
    public function passwordAction()
    {
        /**
         * @var Form $form
         * @var UserModel $model
         */
        // @TODO Перевести под новую модель
        $this->request->setTitle($this->t('user','Change password'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('user/cabinet',$this->t('user','User cabiner'))
            ->addPiece(null, $this->request->getTitle());


        $model  = $this->getModel('User');

        $form = $model->getPasswordForm();

        $user = $this->auth->currentUser();
        //printVar($this->user->getData());

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $pass_hash  = $user->generatePasswordHash(
                    $form->getField('password')->getValue(), $user->get('solt')
                );
                //$pass_hash = $this->user->generatePasswordHash( $form->password, $this->user->get('solt') );

                if ( $user->get('password') == $pass_hash )
                {
                    //$this->request->addFeedback('Пароль введен верно');

                    if ( strcmp( $form->getField('password1')->getValue(), $form->getField('password2')->getValue() ) === 0 ) {
                        $user->changePassword( $form->getField('password1')->getValue() );
                        $this->request->addFeedback($this->t('user','Password successfully updated'));
                        return $this->tpl->fetch('user.password_success');
                    }
                    else {
                        $this->request->addFeedback($this->t('user','You must enter a new password 2 times'));
                    }

                } else {
                    $this->request->addFeedback($this->t('user','Password is not correct'));
                }

            } else {
                $this->request->addFeedback( $form->getFeedbackString() );
            }
        }

        $this->tpl->assign(array(
            'form'  => $form
        ));

        return $this->tpl->fetch('user.password');
    }

}
