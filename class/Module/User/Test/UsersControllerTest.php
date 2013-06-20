<?php
namespace Module\User\Test;

use Module\User\Controller\UserController;
use Sfcms\Test\TestCase;

/**
 * Test class for UserController.
 * Generated by PHPUnit on 2012-04-20 at 20:50:17.
 */
class UsersControllerTest extends TestCase
{
    /**
     * @var \Sfcms\Request
     */
    protected $request;

    /**
     * Инициализвция
     */
    public function testInit()
    {
        $controller = new UserController($this->request);
        $controller->init();
        $this->assertEquals('inner', $this->request->getTemplate());
    }

    /**
     * Права доступа
     */
    public function testAccess()
    {
        $controller = new UserController($this->request);
        $access = $controller->access();
        $this->assertArrayHasKey(USER_ADMIN, $access);
        $this->assertContains('admin', $access[USER_ADMIN]);
        $this->assertContains('adminEdit', $access[USER_ADMIN]);
        $this->assertContains('save', $access[USER_ADMIN]);
    }

    /**
     * Действие по умолчанию
     */
    public function testIndexAction()
    {
        $response = $this->runController('user', 'index');
        $this->assertEquals(302, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Redirecting to /user/login', $crawler->filterXPath('//title')->text());
    }


    /**
     * Действие админа
     */
    public function testAdminAction()
    {
        $response = $this->runController('user', 'index');
        $response = $this->followRedirect($response);
        $crawler = $this->createCrawler($response);
        $form = $crawler->filterXPath('//form');
        $this->assertEquals(1, $form->count());
        $this->assertEquals('form_login', $form->attr('id'));
    }

    /**
     * Редактирование пользователя в админке
     */
    public function testAdminEditAction()
    {
        $response = $this->runController('user', 'adminEdit');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirection());
        $crawler = $this->createCrawler($response);
        $this->assertEquals(1, $crawler->filterXPath('//title')->count());
        $this->assertEquals('Redirecting to /user/login', $crawler->filterXPath('//title')->text());

        $this->session->set('user_id', 1);
        $response = $this->runController('user', 'adminEdit');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Добавить пользователя / SiteForeverCMS', $crawler->filterXPath('//title')->text());
        $form = $crawler->filterXPath('//form');
        $this->assertEquals('form_user', $form->attr('id'));
    }

    /**
     * Сохранение
     */
    public function testSaveAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runController('user', 'save');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('Data not sent', trim($crawler->filterXPath('//div[@id="workspace"]')->text()));
    }

    /**
     * Кабинет
     */
    public function testCabinetAction()
    {
//        $return = $this->controller->cabinetAction();
    }

    /**
     * Правка профиля
     */
    public function testEditAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runController('user', 'login');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Redirecting to /user/cabinet', $crawler->filterXPath('//title')->text());

        $response = $this->runController('user', 'cabinet');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Кабинет пользователя / SiteForeverCMS', $crawler->filterXPath('//title')->text());
        $this->assertEquals('Кабинет пользователя', $crawler->filterXPath('//h1')->text());
    }

    /**
     * Регистрация
     */
    public function testRegisterAction()
    {
        $response = $this->runController('user', 'register');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $form = $crawler->filterXPath('//form[@id="form_register"]');
        $this->assertEquals(1, $form->count());
        $this->assertEquals('', $form->attr('action'));
        $this->assertEquals('form_register', $form->attr('name'));
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_email"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_login"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_password"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_fname"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_lname"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_phone"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_captcha"]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//input[@id="register_submit"]')->count());

        $_SESSION['_sf2_attributes']['captcha_code'] = 'test';
//        $this->request->getSession()->set('captcha_code', 'test');
//        $this->request->getSession()->save();
        $this->request->setMethod('POST');
        $_POST = array('register' => array(
            'email' => 'test_user@example.com',
            'login' => 'test_user',
            'password' => 'test_test',
            'fname' => 'test',
            'lname' => 'user',
            'phone' => '89005555555',
            'captcha' => 'test',
        ));
        $response = $this->runController('user', 'register');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Регистрация прошла успешно. На Ваш Email отправлена ссылка для подтверждения регистрации.',
            $crawler->filterXPath('//div[@class="alert"]')->text()
        );


        $_POST = array('login' => array(
            'login' => 'test_user',
            'password' => 'test_test',
        ));

        $response = $this->runController('user', 'register');
        $crawler = $this->createCrawler($response);


        $this->assertEquals(0, $crawler->filterXPath('//div[@class="alert"]')->count());
    }


    /**
     * Вход
     * @ depends testRegisterAction()
     */
    public function testLoginAction()
    {
        $response = $this->runController('user', 'login');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $form = $crawler->filterXPath('//form');
        $this->assertEquals(1, $form->count());
        $this->assertEquals('form_login', $form->attr('id'));
        $this->assertEquals('form_login', $form->attr('name'));

        $_POST['login'] = array(
            'login' => 'test_user',
            'password' => 'test_test',
        );
        $response = $this->runController('user', 'login');
        $crawler = $this->createCrawler($response);


        $this->assertEquals('Ваша учетная запись отключена', $crawler->filterXPath('//div[@class="alert alert-error"]')->text());
    }


    /**
     * Восстановление
     */
    public function testRestoreAction()
    {
        $response = $this->runController('user', 'restore');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $form = $crawler->filterXPath('//form');
        $this->assertEquals(1, $form->count());
        $this->assertEquals('form_restore', $form->attr('id'));
        $this->assertEquals('form_restore', $form->attr('name'));
        $this->assertEquals(3, $form->filterXPath('//input')->count());
    }

    public function testRecoveryAction()
    {
        $response = $this->runController('user', 'recovery');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Не указаны параметры восстановления',
            trim($crawler->filterXPath('//div[@class="alert alert-error"]')->text()));

        $this->request->query->set('email', 'sdsadsd@ermin.ru');
        $this->request->query->set('code', '123232afsdfsdfs');
        $response = $this->runController('user', 'recovery');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Ваш email не найден',
            trim($crawler->filterXPath('//div[@class="alert alert-error"]')->text()));

        $this->request->query->set('email', 'admin@ermin.ru');
        $this->request->query->set('code', '123232afsdfsdfs');
        $response = $this->runController('user', 'recovery');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Неверный код восстановления',
            trim($crawler->filterXPath('//div[@class="alert alert-error"]')->text()));
    }

    /**
     * Пароль
     */
    public function testPasswordAction()
    {
        $response = $this->runController('user', 'password');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Redirecting to /user/login', $crawler->filterXPath('//title')->text());

        $this->session->set('user_id', 1);
        $response = $this->runController('user', 'password');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filterXPath('//form');
        $this->assertEquals(1, $form->count());
        $this->assertEquals('form_password', $form->attr('id'));
        $this->assertEquals('form_password', $form->attr('name'));
        $this->assertEquals(4, $form->filterXPath('//input')->count());

        $_POST = array('password' => array(
            'password'  => 'admin',
            'password1' => 'admin1',
            'password2' => 'admin2',
        ));
        $this->session->set('user_id', 1);
        $response = $this->runController('user', 'password');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Нужно ввести новый пароль 2 раза', $crawler->filterXPath('//div[@class="alert"]')->text());


        $_POST = array('password' => array(
            'password'  => 'admin',
            'password1' => 'adminpass',
            'password2' => 'adminpass',
        ));
        $this->session->set('user_id', 1);
        $response = $this->runController('user', 'password');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Пароль успешно изменен', $crawler->filterXPath('//div[@class="alert"]')->text());
    }
}
