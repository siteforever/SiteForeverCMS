<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    public function testTranslit()
    {
        $str    = 'Привет мир!';
        $this->assertEquals('privet_mir', Sfcms_i18n::getInstance()->translit( $str ));
    }
}
