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
        // подтверждение регистрации
        if ($confirm && $userid) {
            $this->request->setTitle('Подтверждение регистрации');
            $this->getTpl()->getBreadcrumbs()->addPiece('index', t('Home'))->addPiece(
                    'user',
                    t('user', 'Sign in site')
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

                return array('error'=>false, 'success' => 1, 'message'=>t('Регистрация успешно подтверждена'));
            } else {
                return array('error'=>true, 'message'=>'Ваш аккаунт не подвержден, обратитесь к '
                . '<a href="mailto:'.$this->config->get('admin').'">администрации сайта</a>'
                );
            }
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

        $model  = $this->getModel('User');

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

        $paging = $this->paging($count, 25, '/admin/user/page%page%');

        $criteria['limit']  = $paging['offset'].', '.$paging['perpage'];
        $criteria['order']  = 'login';

        $users = $model->findAll($criteria);

        $this->tpl->assign('users', $users);
        $this->tpl->assign('paging', $paging);
        $this->tpl->assign('groups', $this->config->get('users.groups'));

        return $this->tpl->fetch('user.admin');
    }

    /**
     * Редактирование пользователя в админке
     * @params int $id
     * @return array
     */
    public function adminEditAction( $id )
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
//        setcookie('sxd', null, null, '/runtime/sxd/');
//        $this->app()->getSession()->sxd_auth = 0; // Авторизация Sypex Dumper
//        $this->app()->getSession()->sxd_conf = null;
        $this->app()->getAuth()->logout();
        return $this->redirect('user/login');
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
            return $this->redirect('user/cabinet');
        }

        // вход в систему
        $form = $model->getLoginForm();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $result = $this->login($form->login, $form->password);
                if (!$result['error']) {
                    return $this->redirect($this->request->server->get('HTTP_REFERER'));
                } else {
                    $this->getTpl()->assign($result);
                }
            }
        }
        $this->request->setTitle(t('user','Sign in site'));
        $this->tpl->assign('form', $form );

        return $this->tpl->fetch('user.login');
    }

    /**
     * Логин
     * @param string $login
     * @param string $password
     */
    private function login( $login, $password )
    {
        if ( $password == '' ) {
            return array('error'=>1, 'message'=>t('user','Empty password'));
        }

        /** @var User $user */
        $user = $this->getModel('User')->find(array(
            'cond' => 'login = ?',
            'params'=> array($login),
        ));

        if ( $user ) {
            //print_r( $user->getAttributes() );
            if ( $user->perm < USER_USER ) {
                return array('error' => 1, 'message' => t('user','Not enough permissions'));
            }

            if ( $user->status == 0 ) {
                return array('error'=>1, 'message'=>t('user','Your account has been disabled'));
            }

            $password = $this->app()->getAuth()->generatePasswordHash( $password, $user->solt );

            //print $user->password.' == '.$password;

            if ( $password != $user->password ) {
                return array('error' => 1, 'message'=>t('user','Your password is not suitable'));
            }

            $this->app()->getAuth()->currentUser($user);

//            if ( $user->perm == USER_ADMIN ) {
                // Авторизация Sypex Dumper
//                $this->app()->getSession()->sxd_auth    = 1;
//                $this->app()->getSession()->sxd_conf    = $this->app()->getConfig()->get('db');
//            }

            return array('error' => 0, 'message'=>t('user','Authorization was successful'));
        }

        return array('error' => 1, 'message'=>t('user','Your login is not registered'));
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
            ->addPiece('user/cabinet',t('user','User cabiner'))
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
            ->addPiece('user/login',t('user','Sign in site'))
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

        $obj['solt']    = $this->app()->getAuth()->generateString(8);
        $obj['password']= $this->app()->getAuth()->generatePasswordHash($obj->password, $obj->solt);
        $obj['perm']    = USER_GUEST;
        $obj['status']  = 0;
        $obj['confirm'] = md5($obj['solt'] . md5(microtime(1) . $obj['solt']));

        $obj['date']   = time();
        $obj['last']   = time();

        $model = $this->getModel('User');

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

            sendmail(
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
     */
    public function recoveryAction( $email, $code )
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle(t('user','Password recovery'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Main'))
            ->addPiece('user/login',t('user','Sign in site'))
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
                    $pass = $this->app()->getAuth()->generateString( 8 );
                    $user->changePassword( $pass );

                    $this->tpl->assign(array(
                        'pass'      => $pass,
                        'login'     => $user['login'],
                        'sitename'  => $this->config->get('sitename'),
                        'loginform' => $this->config->get('siteurl').
                                $this->router->createLink("user/login")
                    ));

                    sendmail(
                        $this->config->get('admin'),
                        $email,
                        t('user','New password'), $this->tpl->fetch('user.mail.recovery')
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
         * @var User $user
         * @var UserModel $model
         */
        // @TODO Перевести под новую модель

        $this->request->setTemplate('inner');
        $this->request->setTitle(t('user','Password recovery'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Main'))
            ->addPiece('user/login',t('user','Sign in site'))
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
                    sendmail(
                        $this->config->get('admin'),
                        $form->email,
                        t('user','Password recovery'),
                        $this->tpl->fetch('user.mail.restore')
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
         * @var Form $form
         * @var UserModel $model
         */
        // @TODO Перевести под новую модель
        $this->request->setTitle(t('user','Change password'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece('user/cabinet',t('user','User cabiner'))
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
                        return $this->tpl->fetch('user.password_success');
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

        return $this->tpl->fetch('user.password');
    }

}
