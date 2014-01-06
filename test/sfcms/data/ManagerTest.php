<?php
/**
 * Testing data manager
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace sfcms\data;


class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DataManager */
    private $manager;

    protected function setUp()
    {
        parent::setUp();

        $this->manager = \App::cms()->getContainer()->get('data.manager');
    }

    public function testGetModelList()
    {
        $modelList = $this->manager->getModelList();
        $this->assertTrue(is_array($modelList));
        $this->assertArrayHasKey('id', $modelList[0]);
        $this->assertArrayHasKey('alias', $modelList[0]);
        $this->assertArrayHasKey('class', $modelList[0]);
    }
}
