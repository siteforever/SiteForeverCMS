<?php
/**
 * 
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Test;

use Exception;


class PageControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected function setUp()
    {
        $this->setBrowser('*firefox');
        $this->setBrowserUrl('http://cms.sf/');
    }


    /**
     * @covers Module\Page\Test\PageController::editAction
     * @covers Module\Page\Test\PageController::saveAction
     * @covers Module\Page\Test\PageController::hiddenAction
     */
    public function testAdminActions()
    {
        $this->open("http://cms.sf/admin");
        $this->type("login_login", "admin");
        $this->type("login_password", "admin");
        $this->click("id=login_submit");
        $this->waitForPageToLoad("30000");
        $this->click("link=Управление сайтом");
        $this->waitForPageToLoad("30000");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if (!$this->isVisible("xpath=id('loading-application')")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->mouseOver("xpath=id('item1')/a");
        $this->click("xpath=id('item1')//a[@class='add']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=pageCreate")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->type("id=name", "testpage");
        $this->click("xpath=id('pageCreate')//a[@class='btn btn-primary save']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=pageEdit")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("link=Контент");
        $this->type("id=structure_content", "Hello world!");
        $this->click("link=Сохранить изменения");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if (!$this->isVisible("id=pageEdit")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isElementPresent("link=testpage")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("link=testpage");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=pageEdit")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->type("id=structure_name", "testpage2");
        $this->click("link=Сохранить изменения");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if (!$this->isVisible("id=pageEdit")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isElementPresent("link=testpage2")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("xpath=//span[a[contains(text(),'testpage2')]]//a[contains(@class, 'order_hidden')]");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ("Выкл" == $this->getText("xpath=//span[a[contains(text(),'testpage2')]]//a[contains(@class, 'order_hidden')]")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("xpath=//span[a[contains(text(),'testpage2')]]//a[contains(@class, 'order_hidden')]");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ("Вкл" == $this->getText("xpath=//span[a[contains(text(),'testpage2')]]//a[contains(@class, 'order_hidden')]")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->mouseOver("link=testpage2");
        $this->chooseCancelOnNextConfirmation();
        $this->click("xpath=//span[a[contains(text(),'testpage2')]]//a[@class='do_delete']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isConfirmationPresent()) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->getConfirmation();
        $this->chooseOkOnNextConfirmation();
        $this->click("xpath=//span[a[contains(text(),'testpage2')]]//a[@class='do_delete']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isConfirmationPresent()) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->getConfirmation();
        $this->click("link=*Выход");
        $this->waitForPageToLoad("30000");
    }

}