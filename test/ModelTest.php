<?php

class Model_Test extends Model {}

/**
 * Test class for Model.
 * Generated by PHPUnit on 2011-02-07 at 18:49:00.
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ( is_null( $this->object ) ) {
            $this->object = Model::getModel('Test');
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testGetDB().
     */
    public function testGetDB()
    {
        $this->assertTrue( $this->object->getDB() instanceof DB );
    }

    /**
     * @todo Implement testApp().
     */
    public function testApp()
    {
        $this->assertTrue( $this->object->app() instanceof Application_Abstract );
    }

    /**
     * @todo Implement testGetModel().
     */
    public function testGetModel()
    {
        $this->assertTrue( $this->object->getModel('Test') instanceof Model_Test );
    }

    /**
     * @todo Implement testCreateObject().
     */
    public function testCreateObject()
    {
        $obj = $this->object->createObject();
        if ( ! $obj ) {
            $this->fail('Created object '.var_export($obj, true));
        }
        $this->assertTrue( $obj instanceof Data_Object_Test );
        $obj->markClean();
    }

    /**
     * @todo Implement testObjectClass().
     */
    public function testObjectClass()
    {
        $this->assertEquals($this->object->objectClass(), 'Data_Object_Test');
    }

    /**
     * @todo Implement testTableClass().
     */
    public function testTableClass()
    {
        $this->assertEquals($this->object->tableClass(), 'Data_Table_Test');
    }

    /**
     * @todo Implement testGetTable().
     */
    public function testGetTable()
    {
        $this->assertTrue( $this->object->getTable() instanceof Data_Table_Test );
    }

    /**
     * @todo Implement testGetTableName().
     */
    public function testGetTableName()
    {
        $this->assertEquals( $this->object->getTableName(), DBPREFIX.'test' );
    }

    /**
     * @todo Implement testSave().
     */
    public function testSave()
    {
        $obj1   = $this->object->createObject(array('value'=>'val1'));
        $obj2   = $this->object->createObject(array('value'=>'val2'));
        $this->assertNotNull( $this->object->save($obj1) );
        $this->assertNotNull( $this->object->save($obj2) );
    }

    /**
     * @todo Implement testCount().
     */
    public function testCount()
    {
        //$this->assertEquals($this->object->count(), 2);
    }

    /**
     * @todo Implement testFind().
     */
    public function testFind()
    {
        $obj    = $this->object->find(2);
        $this->assertNotNull($obj);
        $this->assertEquals($obj->getId(), 2);
        $this->assertEquals($obj->value, 'val2');

        $obj    = $this->object->find(1);
        $this->assertNotNull($obj);
        $this->assertEquals($obj->getId(), 1);
        $this->assertEquals($obj->value, 'val1');
    }

    /**
     * @todo Implement testFindAll().
     */
    public function testFindAll()
    {
        $all    = $this->object->findAll();
        $this->assertEquals( count($all), 2 );
        /*$this->assertEquals( $all, array(
            array('id'=>1,'value'=>'val1'),
            array('id'=>2,'value'=>'val2'),
        ));*/
    }

    /**
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
        $this->object->delete(1);
        $this->assertNull( $this->object->find(1) );

        $this->object->delete(2);
        $this->assertNull( $this->object->find(2) );
        
        $this->assertEquals($this->object->count(), 0);

        $pdo    = $this->object->getDB()->getResource();
        $pdo->exec("DROP TABLE `{$this->object->getTable()}`");
    }
}
?>