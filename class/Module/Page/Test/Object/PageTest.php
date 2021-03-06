<?php
/**
 * Тест объекта страницы
 */
namespace Module\Page\Test\Object;

use Module\Page\Object\Page;
use Sfcms\Data\Watcher;
use Sfcms\Model;

class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        Watcher::instance()->clear();
    }

    public function testGetKeywordsList()
    {
        /** @var Page $page */
        $page = \App::cms()->getModel('Page')->createObject();
        $page->content = <<<PAGE
<h2>Реферат по физике</h2>
<h1 style="color:black; margin-left:0;">Тема: «Векторный бозе-конденсат: предпосылки и развитие»</h1>
<p>Еще в ранних работах Л.Д.Ландау показано, что примесь коаксиально отклоняет лептон без обмена зарядами или спинами. Гамма-квант полупрозрачен для жесткого излучения. Галактика сингулярно тормозит кристалл без обмена зарядами или спинами. Кристаллическая решетка, несмотря на некоторую вероятность коллапса, доступна. </p>
<p>Лептон полупрозрачен для жесткого излучения. Кварк, в отличие от классического случая, представляет собой квант в полном соответствии с законом сохранения энергии. Галактика, в рамках ограничений классической механики, неустойчива. Колебание стабильно. </p>
<p>Идеальная тепловая машина отталкивает газ как при нагреве, так и при охлаждении. Силовое поле  по определению отрицательно заряжено. Колебание когерентно восстанавливает электронный лазер  - все дальнейшее далеко выходит за рамки текущего исследования и не будет здесь рассматриваться. Плазма индуцирует кристалл, однозначно свидетельствуя о неустойчивости процесса в целом. </p>
PAGE;
        $index = $page->getKeywordsList(4);
        $this->assertEquals(
            'излучения, галактика, кристалл, колебание, жесткого, полупрозрачен, обмена, зарядами, спинами, квант, лептон',
            join(', ', $index)
        );
    }
}
