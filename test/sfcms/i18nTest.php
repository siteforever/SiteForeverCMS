<?php
/**
 * Тестирование интенационализации
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

use Sfcms\i18n;

class Sfcms_i18nTest extends PHPUnit_Framework_TestCase
{
    public function testTranslit()
    {
        $i18n = Sfcms::i18n();
        $this->assertEquals('privet-mir', $i18n->translit( 'Привет мир!' ));
        $this->assertEquals('svobodu-kevinu-mitniku', $i18n->translit( 'Свободу..Кевину!Митнику.' ));
    }

    public function testWrite()
    {
        $i18n = Sfcms::i18n();
        $this->assertEquals('Панель управления', $i18n->write('Control panel'));
        $this->assertEquals('Модуль страницы', $i18n->write('page','Page module'));
        $this->assertEquals('Страница Тест', $i18n->write('Page :name', array(':name'=>'Тест')));
        $this->assertEquals('Страница Тест', $i18n->write('page', 'Page :name', array(':name'=>'Тест')));
    }
}
