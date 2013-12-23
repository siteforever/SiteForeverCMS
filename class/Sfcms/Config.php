<?php
namespace Sfcms;

use Symfony\Component\DependencyInjection\Container;

/**
 * Контейнер конфигурации
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */
class Config
{
    private $config;

    /**
     * Если передан null, то ищет файл по константе CONFIG
     * Если передана строка, то ищет файл по этой строке
     * Если передан массив, то принимает его как конфиг
     *
     * @param string|array $cfg
     * @param Container $container
     * @throws Exception
     */
    public function __construct($cfg, Container $container = null)
    {
        if (!is_string($cfg)) {
            throw new \InvalidArgumentException('$cfg may be only the path');
        }
        $this->config = @include($cfg);
        if ($container) {
            $this->registerParameters('', $this->config, $container);
            foreach($container->getParameterBag()->all() as $key => $parameter) {
                $this->set($key, $parameter);
            }
        }
        return $this;
    }

    /**
     * Registering config parameters to service container
     * @param           $ns
     * @param array     $config
     * @param Container $container
     */
    protected function registerParameters($ns, array $config, Container $container)
    {
        foreach ($config as $key => $val) {
            $container->setParameter($ns . $key, $val);
//            if (is_scalar($val)) {
//            } else {
//                $this->registerParameters($ns . $key . '.', $val, $container);
//            }
        }
    }

    /**
     * Установить значение
     * @param $key
     * @param $val
     * @return void
     */
    public function set($key, $val)
    {
        $path = explode('.', $key);
        if (count($path) == 1) {
            $this->config[$key] = $val;
        } else {
            $this->seti($path, $val);
        }
    }

    /**
     * Устанавливает для ключа значение по умолчанию.
     * Если значения присутствуют, то ини не будут изменены, а только дополнены.
     * Удобно для присваивания массивами.
     * @param string $key
     * @param array $default
     * @return void
     */
    public function setDefault($key, $default)
    {
        $config = $this->get($key);
        if ($config && is_array($config) && is_array($default)) {
            $config = $config + $default;
        } elseif (is_null($config)) {
            $config = $default;
        }
        $this->set($key, $config);
    }

    /**
     * Получить значение
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $path = explode('.', $key);
        if (count($path) == 1) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            }
            return $default;
        } else {
            return $this->geti($path, $default);
        }
    }

    /**
     * Получить значение по алиасу
     * @param array $path
     * @param $default
     * @return mixed|null
     */
    protected function geti($path, $default = null)
    {
        $data = $this->config;
        foreach ($path as $part) {
            if (isset($data[$part])) {
                $data = $data[$part];
            } else {
                return $default;
            }
        }
        return $data;
    }

    /**
     * Установить свое значение по алиасу
     * @param $alias
     * @param $value
     */
    protected function seti($path, $value)
    {
        $data = &$this->config;
        foreach ($path as $part) {
            if (!isset($data[$part])) {
                $data[$part] = array();
            }
            $data =& $data[$part];
        }
        $data = $value;
    }

}
