<?php
class Model_PageTest extends PHPUnit_Framework_TestCase
{
    /** @var Model_Page */
    protected $model;

    /** @var Db */
    protected $db;

    protected function setUp()
    {
        $this->model = Sfcms_Model::getModel('Page');
        $this->db    = Sfcms_Model::getDB();
        Data_Watcher::instance()->clear();
    }

    /**
     * Тест выбирает страницы, принадлежащие главной и перемешивает их.
     * Потом проверяет, что они правильно перемешались.
     */
    public function testResort()
    {
        $data = $this->db->fetchAll(
            'SELECT id, pos FROM '.$this->model->getTable().' WHERE parent = 1'
        );
        $posOld = array_map(function($d){
            return $d['id'];
        }, $data);
        $posNew = $posOld;
        shuffle( $posNew );
        $this->model->resort( $posNew );
        Data_Watcher::instance()->performOperations();

        $data2 = $this->db->fetchAll('SELECT id, pos FROM '.$this->model->getTable().' WHERE id IN ('.join(',',$posOld).')');
        $posCheck = array_flip( $posNew );
        foreach( $data2 as $d ) {
            $this->assertEquals( $posCheck[$d['id']], $d['pos'], 'Sort order not changed' );
        }
        $this->model->resort( $posOld );
        Data_Watcher::instance()->performOperations();
    }

}
