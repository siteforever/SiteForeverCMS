<?php
use Module\Page\Model\PageModel;
use Sfcms\Data\Watcher;
use Sfcms\Model;
use Sfcms\db;

class Model_PageTest extends PHPUnit_Framework_TestCase
{
    /** @var PageModel */
    protected $model;

    /** @var db */
    protected $db;

    protected function setUp()
    {
        $this->model = Model::getModel('Page');
        $this->db    = Model::getDB();
        Watcher::instance()->clear();
    }

    public function testModelClass()
    {
        $this->assertEquals('page', $this->model->eventAlias());
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

        Watcher::instance()->performOperations();

        $data2 = $this->db->fetchAll('SELECT id, pos FROM '.$this->model->getTable().' WHERE id IN ('.join(',',$posOld).')');
        $posCheck = array_flip( $posNew );
        foreach( $data2 as $d ) {
            $this->assertEquals( $posCheck[$d['id']], $d['pos'], 'Sort order not changed' );
        }
        $this->model->resort( $posOld );
        Watcher::instance()->performOperations();
    }

}
