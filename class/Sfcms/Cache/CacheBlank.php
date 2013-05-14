<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Cache;


class CacheBlank implements CacheInterface
{
    public function __construct($liveCycle)
    {
    }

    public function isNotExpired($key)
    {
        return false;
    }

    public function get($key)
    {
        return null;
    }

    public function set($key, $value)
    {
    }
}
