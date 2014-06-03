<?php
/**
 * This file is part of the SiteForever package.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Data;


use Sfcms\Data\DataManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DataManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DataManager */
    protected $dataManager;

    protected function setUp()
    {
        parent::setUp();
        $this->dataManager = \App::cms()->getContainer()->get('data.manager');
    }

    /**
     * @covers Sfcms\Data\DataManager::getModel
     * @covers Sfcms\Data\DataManager::getModelId
     * @covers Sfcms\Data\DataManager::getModelList
     */
    public function testGetModel()
    {
        $testModel = $this->dataManager->getModel('test');
        $this->assertTrue($testModel instanceof \Module\System\Model\TestModel);
        $testModel = $this->dataManager->getModel('Test');
        $this->assertTrue($testModel instanceof \Module\System\Model\TestModel);
        $testModel = $this->dataManager->getModel('system.test');
        $this->assertTrue($testModel instanceof \Module\System\Model\TestModel);
        try {
            $this->dataManager->getModel('huy');
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('Model huy not was defined');
    }

}
