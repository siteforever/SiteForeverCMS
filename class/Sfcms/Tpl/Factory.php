<?php
/**
 * Стратегия выбора шаблонизатора
 * @author KelTanas
 */

namespace Sfcms\Tpl;

use Sfcms\Exception;
use Sfcms\Tpl\Smarty;
use Sfcms\Tpl\Driver;
use Sfcms\Kernel\KernelBase as Service;

class Factory
{
    /**
     * Вернет инстанс шаблонизатора
     * @param string $driver
     *
     * @return Driver
     * @throws Exception
     */
    static function create($config)
    {
        $driver = $config['driver'];
        if (class_exists($driver)) {
            return new $driver($config);
        }
        throw new Exception("Templates driver '{$driver}' not found");
    }
}