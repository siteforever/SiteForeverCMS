<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\Event;

use Symfony\Component\EventDispatcher\Event;

class DatabaseEvent extends Event
{
    const TABLE_CREATE = 'database.table.create';
}
