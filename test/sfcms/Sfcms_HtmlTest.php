<?php
/**
 * Test Sfcms/Html class
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

//require_once '../../class/sfcms.php';

class Sfcms_HtmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Sfcms\Html
     */
    protected $html;


    protected function setUp()
    {
        $this->html = Sfcms::html();
    }


    public function testLink()
    {
        $link = $this->html->link('test','test/admin',array(
            'class'      => 'test',
            'htmltarget' => '_blank',
            'data-id'    => 12,
            'nofollow'   => true,
        ));
        $this->assertEquals('<a rel="nofollow" class="test" target="_blank" data-id="12" href="/test/admin">test</a>', $link);

        $link = $this->html->link('Page edit','#',array(
            'controller' => 'page',
            'action'     => 'edit',
            'id'         => 12,
            'class'      => 'test',
            'htmltarget' => '_blank',
            'data-id'    => 12,
            'nofollow'   => true,
        ));
        $this->assertEquals('<a rel="nofollow" class="test" target="_blank" data-id="12" href="/page/edit/id/12">Page edit</a>', $link);

        $link = $this->html->link('test', '#', array(
            'controller' => 'page',
            'action'     => 'admin',
        ));
        $this->assertEquals('<a href="/page/admin">test</a>', $link);
        $link = $this->html->link('test', '#', array(
            'controller' => 'page',
            'action'     => 'admin',
            'nofollow'   => false,
        ));
        $this->assertEquals('<a href="/page/admin">test</a>', $link);

        $link = $this->html->link('test', 'http://example.com', array('val'=>'test'));
        $this->assertEquals('<a href="http://example.com">test</a>', $link);

        $link = $this->html->link('test', '#anchor', array('val'=>'test'));
        $this->assertEquals('<a href="#anchor">test</a>', $link);
    }

    public function testIcon()
    {
        $icon = $this->html->icon('pencil', 'Edit');
        $this->assertEquals("<i class='sfcms-icon sfcms-icon-pencil' title='Edit'></i>", $icon);
    }
}
