<?php

class CaptchaControllerCest
{
    public function testIndexAction(\AcceptanceTester $I)
    {
        $I->amOnPage('/?controller=captcha');
        $I->seeHttpHeader('content-type', 'image/png');
    }
}