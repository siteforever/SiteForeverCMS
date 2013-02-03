<?php
/**
 * Связь "принадлежит"
 * @author: keltanas
 */
//require_once SF_PATH . '/class/Sfcms/Data/Relation.php';
//require_once SF_PATH . '/class/Sfcms/Data/Relation/Belongs.php';

use Module\Page\Object\Page;
use Sfcms\Data\Relation\Belongs;
use Sfcms\Data\Watcher;
use Sfcms\Model;

class Sfcms_Data_Relation_Belongs  extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Watcher::instance()->clear();
    }

    public function testFind()
    {
        $model = Model::getModel('Page');
        $page = $model->find( 2 );

        // $parent = $page->Parent;

        $belongs = new Belongs('Parent', $page);
        $parent = $belongs->find();

        $this->assertEquals( $parent->id, $page->parent );
    }

    public function testWith()
    {
        $model = Model::getModel('Page');
        $model->with('Parent')->findAll( 'parent = ?', array(1), '', 1 );
        $objects = Watcher::instance()->dumpAll();
        $this->assertArrayHasKey('Module\Page\Object\Page.2', $objects);
        $this->assertArrayHasKey('Module\Page\Object\Page.1', $objects);
    }
}