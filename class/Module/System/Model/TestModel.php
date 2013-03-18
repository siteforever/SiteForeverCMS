<?php
/**
 * Модель для тестирования
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Model;

use Sfcms\Model;

class TestModel extends Model
{
    public function objectClass()
    {
        return '\Module\System\Object\Test';
    }

    public function eventAlias()
    {
        return 'test';
    }
}
