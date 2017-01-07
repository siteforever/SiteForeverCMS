<?php

class UserControllerCest
{
    public function login(\AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->fillField('#login_login', 'admin1');
        $I->fillField('#login_password', '123456789');
        $I->click('#login_submit');
        $I->see('Кабинет пользователя', 'h1');
        $I->see('Управление сайтом', 'a');

        $I->see('Вы вошли как: admin1', 'li');
        $I->see('Ваш статус: Администратор ', 'li');
    }

    public function redirectToLogin(\AcceptanceTester $I)
    {
        $I->amOnPage('/user');
        $I->seeCurrentUrlEquals('/user/login');
    }
}