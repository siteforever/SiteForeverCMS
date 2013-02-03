<?php
namespace Sfcms;
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
     * @param string|array $cfg_file
     */
    public function __construct($cfg_file = null)
    {
        if (is_null($cfg_file)) {
            if (defined('CONFIG')) {
                $cfg_file = 'protected/config/' . CONFIG . '.php';
            }
        }

        if (is_array($cfg_file)) {
            $result = array();
            foreach ( $cfg_file as $cfg ) {
                $config = require $cfg;
                $result = array_merge( $result, $config );
            }
            $this->config = $result;
            return;
        }

        if (is_string($cfg_file) && file_exists($cfg_file)) {
            $this->config = require $cfg_file;
            return;
        }
//        throw new Application_Exception('Configuration file "'.$cfg_file.'" not found');
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
        }
        else {
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
            $config = array_merge($default, $config);
        }
        elseif (is_null($config)) {
            $config = $default;
        }
        $this->set($key, $config);
    }

    /**
     * Получить значение
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $path = explode('.', $key);
        if (count($path) == 1) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            }
            return null;
        }
        else {
            return $this->geti($path);
        }
    }

    /**
     * Получить значение по алиасу
     * @param array $path
     * @return mixed|null
     */
    protected function geti($path)
    {
        $data = $this->config;
        foreach ($path as $part) {
            if (isset($data[$part])) {
                $data = $data[$part];
            }
            else {
                return null;
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