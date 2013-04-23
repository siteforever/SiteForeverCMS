<?php
/**
 * Тест главной страницы
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

class MainTest extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/home/keltanas/www/sf/cms/test/screenshots';
    protected $screenshotUrl = 'http://cms.sf/screenshots';

    public function setUp()
    {
        $this->setBrowser('*firefox');
        $this->setBrowserUrl('http://cms.sf/');
    }

    public function testTitle()
    {
        $this->open('http://cms.sf/');
        $this->assertTitle('Главная / SiteForeverCMS');
    }
}