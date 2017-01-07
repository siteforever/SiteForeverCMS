<?php

class PageControllerCest
{
    public function mainPage(\AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('SiteForeverCMS', 'h1');
        $I->see('Информационная страница', 'p');

        $I->click('О компании');
        $I->see('О компании', 'h1');

        $I->click('Контакты');
        $I->see('Контакты', 'h1');
    }
}