<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Cache;


class CacheFile implements CacheInterface
{
    protected $liveCycle;

    public function __construct($liveCycle = 3600)
    {
        $this->liveCycle = $liveCycle;
    }

    public function isNotExpired($key)
    {
        $fileName = $this->getFileName($key);
        return file_exists($fileName) && filemtime($fileName) + $this->liveCycle > time();
    }

    public function get($key)
    {
        $fileName = $this->getFileName($key);
        if (!file_exists($fileName)) {
            return null;
        }
        return file_get_contents($fileName);
    }

    public function set($key, $value)
    {
        $fileName = $this->getFileName($key);
        return file_put_contents($fileName, $value);
    }

    protected function getFileName($key)
    {
        $cache_dir = ROOT . '/runtime/cache';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }
        return $cache_dir . '/' . md5($key) . '.cache';
    }
}
