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
     * Если передан null, то ищет файл по константе CONFIG
     * Если передана строка, то ищет файл по этой строке
     * Если передан массив, то принимает его как конфиг
     *
     * @param string|array|null $cfg_file
     * @throws Exception
     */
    public function __construct($cfg_file = null)
    {
        if (is_null($cfg_file) && defined('CONFIG')) {
            $cfg_file = 'application/' . CONFIG . '.php';
        }
        if (is_array($cfg_file)) {
            $this->config = $cfg_file;
            return $this;
        }
        try {
            $this->config = @require_once $cfg_file;
            return $this;
        } catch (\Exception $e) {
        }
        var_dump(get_include_path());
        throw new Exception('Configuration file "'.$cfg_file.'" not found');
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
            }
            else {
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
