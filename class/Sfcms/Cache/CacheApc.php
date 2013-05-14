<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Cache;


class CacheApc implements CacheInterface
{
    private $liveCycle;

    public function __construct($liveCycle)
    {
        $this->liveCycle = $liveCycle;
    }

    public function isNotExpired($key)
    {
        return apc_exists($key);
    }

    public function get($key)
    {
        $val = apc_fetch($key);
        return $val ? $val : null;
    }

    public function set($key, $value)
    {
        return apc_store($key, $value, $this->liveCycle);
    }

}
