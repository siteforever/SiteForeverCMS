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
        $str    = 'Привет мир!';
        $this->assertEquals('privet_mir!', Sfcms_i18n::getInstance()->translit( $str ));
    }
}
