<?php
namespace Sfcms;

use App;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Переводчик
 */

class i18n
{

    private $_lang = null;
    private $_dictionary = null;

    /**
     * @var i18n
     */
    private static $_instance;

    protected $_table = array(
        'кон' => 'con',
        'ком' => 'com',
        'кат' => 'cat',
        'а'   => 'a',
        'б'   => 'b',
        'в'   => 'v',
        'г'   => 'g',
        'д'   => 'd',
        'е'   => 'e',
        'ё'   => 'e',
        'ж'   => 'zh',
        'з'   => 'z',
        'и'   => 'i',
        'й'   => 'i',
        'к'   => 'k',
        'л'   => 'l',
        'м'   => 'm',
        'н'   => 'n',
        'о'   => 'o',
        'п'   => 'p',
        'р'   => 'r',
        'с'   => 's',
        'т'   => 't',
        'у'   => 'u',
        'ф'   => 'f',
        'х'   => 'h',
        'ц'   => 'c',
        'ч'   => 'ch',
        'ш'   => 'sh',
        'щ'   => 'sch',
        'ъ'   => 'j',
        'ы'   => 'y',
        'ь'   => 'j',
        'э'   => 'e',
        'ю'   => 'yu',
        'я'   => 'ya',
        ' '   => '_',
    );

    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF-8', 'en_US', 'English', 'C');
        setlocale(LC_TIME, 'rus', 'ru_RU.UTF-8', 'Russia');

        return self::$_instance = $this;
    }

    public function setLanguage($lang = 'en')
    {
        if (null !== $this->_lang) {
            return;
        }
        $start = microtime(1);
        $this->_lang = $lang;
        $fs = new Filesystem();

        $dictFile = SF_PATH . '/protected/lang/' . $this->_lang . '.php';
        if (!$fs->exists($dictFile)) {
            throw new Exception('Dictionary for language ' . $this->_lang . ' not found in file ' . $dictFile);
        }
        $this->_dictionary = @include($dictFile);

        $dest = ROOT . '/static/i18n';

        if (!$fs->exists($dest)) {
            $fs->mkdir($dest, 0777, true);
        }

        // Prepare dictionary for JS
        $jsDictFile = $dest . '/' . $this->_lang . '.js';
        $jsI18nFile = SF_PATH . '/misc/i18n.js';

        if (!App::isDebug() && file_exists($jsDictFile)) {
            return;
        }

        $f = fopen($jsDictFile, 'a');
        flock($f, LOCK_EX);
        ftruncate($f, 0);
        $jsDict = "// RUNTIME DICTIONARY FILE\n\n" . file_get_contents($jsI18nFile);
        $dictList = glob(dirname($dictFile) . DIRECTORY_SEPARATOR . $this->_lang . DIRECTORY_SEPARATOR . '*.php');
        foreach ($dictList as $file) {
            $this->_dictionary['cat_' . basename($file, '.php')] = @include($file);
        }
        $dict = defined('JSON_UNESCAPED_UNICODE') ? json_encode($this->_dictionary, JSON_UNESCAPED_UNICODE)
            : json_encode($this->_dictionary);

        $jsDict = str_replace('/*:dictionary:*/', 'i18n._dict = ' . $dict . ';', $jsDict);

        fwrite($f, $jsDict, strlen($jsDict));
        flock($f, LOCK_UN);
        fclose($f);
        App::cms()->getLogger()->info('i18n js generation time: ' . round(microtime(1) - $start, 3) . ' sec');
    }

    /**
     * @static
     * @return i18n
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            throw new \RuntimeException('Instance not defined');
        }

        return self::$_instance;
    }

    /**
     * Return translated message
     *
     * @param string $message
     *
     * @return string
     */
    public function write($message)
    {
        switch (func_num_args()) {
            case 1:
                if (isset($this->_dictionary[$message])) {
                    return $this->_dictionary[$message];
                }
                break;
            case 2:
                if (is_string(func_get_arg(1))) {
                    return $this->getCategoryTranslate(func_get_arg(0), func_get_arg(1));
                }
                if (is_array(func_get_arg(1))) {
                    return $this->getCategoryTranslate(null, func_get_arg(0), func_get_arg(1));
                }
                break;
            case 3:
                return $this->getCategoryTranslate(func_get_arg(0), func_get_arg(1), func_get_arg(2));
        }

        return $message;
    }

    /**
     * @param       $category
     * @param       $message
     * @param array $params
     *
     * @return mixed
     * @throws Exception
     */
    protected function getCategoryTranslate($category, $message, $params = array())
    {
        $category = strtolower($category);
        if ($category && !isset($this->_dictionary[$category])) {
            $dictFile = SF_PATH . '/protected/lang/' . $this->_lang . '/' . $category . '.php';
            if (file_exists($dictFile)) {
                $this->_dictionary['cat_' . $category] = @include($dictFile);
            } else {
                $this->_dictionary['cat_' . $category] = array();
            }
        }
        if (null !== $category && isset($this->_dictionary['cat_' . $category][$message])) {
            $message = $this->_dictionary['cat_' . $category][$message];
        } elseif (isset($this->_dictionary[$message])) {
            $message = $this->_dictionary[$message];
        }
        foreach ($params as $key => $val) {
            $message = str_replace($key, $val, $message);
        }

        return $message;
    }

    /**
     * Транслитерация
     * @param string $str
     *
     * @return string
     */
    public function translit($str)
    {
        foreach ($this->_table as $rus => $eng) {
            $str = preg_replace('/' . $rus . '/ui', $eng, $str); // For uft8 support
        }
        $str = preg_replace('/[^a-z0-9]+/i', '-', $str);
        $str = trim($str, '-');

        return $str;
    }
}
