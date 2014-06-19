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
        $this->model = App::cms()->getModel('Page');
        $this->db    = App::cms()->getContainer()->get('db');
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
            sprintf('SELECT id, pos FROM %s WHERE parent = 1', $this->model->getTable())
        );
        $posOld = array_map(function($d){
            return $d['id'];
        }, $data);
        $posNew = $posOld;
        shuffle($posNew);
        $this->model->resort($posNew);

        Watcher::instance()->performOperations();
        Watcher::instance()->clear();

        $data2 = $this->db->fetchAll(
            sprintf('SELECT id, pos FROM %s WHERE id IN (%s)', $this->model->getTable(), join(',',$posOld))
        );
        $posCheck = array_flip( $posNew );
        foreach( $data2 as $d ) {
//            if (42 == $d['id']) continue;
            $this->assertEquals($posCheck[$d['id']], $d['pos'], 'Sort order page.' . $d['id'] . ' not changed');
        }
        $this->model->resort( $posOld );
        Watcher::instance()->performOperations();
        Watcher::instance()->clear();
    }

}
