<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Cache;


interface CacheInterface
{
    public function __construct($liveCycle);

    public function isNotExpired($key);

    public function get($key);

    public function set($key, $value);
}
