<?php
/**
 * Сестирование постраничника
 * @author: keltanas
 * @link http://siteforever.ru
 */
class PagerTest extends PHPUnit_Framework_TestCase
{
    /** @var Pager */
    private $pager;

    protected function setUp()
    {
        $this->pager = new Pager(100,10,'/catalog/admin');
    }


    public function testOne()
    {
        $this->assertEquals(100,$this->pager->count);
        $this->assertEquals(
            'Страницы: 1 - <a href="/catalog/admin/page=2">2</a> - <a href="/catalog/admin/page=3">3</a> - ... - <a href="/catalog/admin/page=10">10</a> - <a href="/catalog/admin/page=2">след &gt;</a>',
            $this->pager->html
        );
    }
}
