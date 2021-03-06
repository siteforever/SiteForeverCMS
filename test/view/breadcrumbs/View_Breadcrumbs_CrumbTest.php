<?php
use Sfcms\View\Breadcrumbs;
use Sfcms\View\Breadcrumbs\Crumb;
/**
 * Test class for View_Breadcrumbs_Crumb.
 * Generated by PHPUnit on 2011-05-24 at 18:39:16.
 */
class View_Breadcrumbs_CrumbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Crumb
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Crumb( 'Заголовок', 'path/to/page' );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testToString()
    {
        $this->assertEquals( "<a href=\"/path/to/page\">Заголовок</a>", (string)$this->object );
    }

}

