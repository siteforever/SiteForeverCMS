<?php
/**
 * Тестирование интенационализации
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Sfcms_i18nTest extends PHPUnit_Framework_TestCase
{
    public function testTranslit()
    {
        $this->assertEquals('privet-mir', Sfcms_i18n::getInstance()->translit( 'Привет мир!' ));
        $this->assertEquals('svobodu-kevinu-mitniku', Sfcms_i18n::getInstance()->translit( 'Свободу..Кевину!Митнику.' ));
    }

    public function testWrite()
    {
        $this->assertEquals('Панель управления', Sfcms_i18n::getInstance()->write('Control panel'));
        $this->assertEquals('Модуль страницы', Sfcms_i18n::getInstance()->write('page','Page module'));
        $this->assertEquals('Страница Тест', Sfcms_i18n::getInstance()->write('Page :name', array(':name'=>'Тест')));
        $this->assertEquals('Страница Тест', Sfcms_i18n::getInstance()->write('page', 'Page :name', array(':name'=>'Тест')));
    }
}
