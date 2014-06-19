<?php
/**
 * Тестирование объекта
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

class Data_Object_ObjectTest extends PHPUnit_Framework_TestCase
{
    /** @var \Module\System\Model\TestModel */
    public $model;

    /** @var \Module\System\Object\Test */
    public $object;

    protected function setUp()
    {
        $this->model = App::cms()->getModel('System.Test');
        $this->object = $this->model->createObject();
    }

    public function testSetPk()
    {
        $this->assertEquals(array('id'), $this->object->pkAsArray());

        $this->object->setId(10);
        $this->assertEquals(10, $this->object->getId());
        $this->assertEquals(array('id' => 10), $this->object->pkValues());
        try {
            $this->object->setId(11);
            $this->fail('Id already exists');
        } catch (\Sfcms\Data\Exception $e) {
        }
    }
}
