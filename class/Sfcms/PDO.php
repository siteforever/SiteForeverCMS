<?php
/**
 * PDO Adapter
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms;

class PDO extends \PDO
{
    public function __construct($params)
    {
        $params = $params + array('dsn'=>null, 'login'=>null, 'password'=>null, 'options'=>null);
        parent::__construct(
            $params['dsn'],
            $params['login'],
            $params['password'],
            $params['options']
        );
    }
}
