<?php
/**
 * Тест отношения один к одному
 * @author: keltanas
 */

//require_once SF_PATH . '/class/Sfcms/Data/Relation.php';
//require_once SF_PATH . '/class/Sfcms/Data/Relation/One.php';

use Sfcms\Data\Relation\One;
use Module\Catalog\Object\Catalog;
use Module\Page\Object\Page;
use Sfcms\Data\Watcher;
use Sfcms\Model;

class Sfcms_Data_Relation_One extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Watcher::instance()->clear();
    }


    public function testFind()
    {
        $model = Model::getModel('Catalog');
        $catalog = $model->find( 17 );

        //$page = $catalog->Page;

        $one = new One( 'Page', $catalog );
        /** @var $page Page */
        $page = $one->find();

        $this->assertEquals( $catalog->getId(), $page->link );
    }

    public function testWith()
    {
        $model = Model::getModel('Catalog');
        $model->with('Page')->findAll('cat = ?', array(1),'', 5);

        $loadedObjects = Watcher::instance()->dumpAll();
        $this->assertArrayNotHasKey('Module\Page\Object\Page.1', $loadedObjects);
        $this->assertArrayNotHasKey('Module\Page\Object\Page.2', $loadedObjects);
        $this->assertArrayHasKey('Module\Page\Object\Page.51', $loadedObjects);
        $this->assertArrayHasKey('Module\Page\Object\Page.52', $loadedObjects);
        $this->assertArrayHasKey('Module\Page\Object\Page.54', $loadedObjects);
        $this->assertArrayHasKey('Module\Page\Object\Page.55', $loadedObjects);
        $this->assertArrayHasKey('Module\Page\Object\Page.57', $loadedObjects);
    }
}