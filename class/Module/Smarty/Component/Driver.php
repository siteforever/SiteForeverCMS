<?php
/**
 * Драйвер для Smarty
 * @author KelTanas
 */
namespace Module\Smarty\Component;

use Sfcms\Tpl\Driver as TplDriver;
use Sfcms\Exception;

class Driver extends TplDriver
{
    private $ext = '';

    private $config = array();

    public function __construct($config, \Smarty $engine)
    {
        $this->config = $config;

        if (!isset($this->config['theme'])) {
            throw new Exception('Theme name in "template[theme]" not defined');
        }

        // класс шаблонизатора
        $this->engine = $engine;
        if ($this->config['caching']) {
            $this->engine->cache_lifetime = $this->config['cache']['livetime'];
        }
        $this->ext    = $this->config['ext'];

        $runtime    = ROOT."/runtime";
        $tpl_c  = $runtime."/templates";
        $cache  = $runtime."/cache";

        $this->setCplDir($tpl_c);
        $this->setCacheDir($cache);

        $this->engine->compile_check = $this->config['compile_check'];
        $this->engine->caching = false;
    }

    public function assign( $params, $value = null )
    {
        $this->engine->assign( $params, $value );
    }

    /**
     * Установка кэширования
     * @param bool $state
     * @return void
     */
    public function caching($state = false)
    {
        $this->engine->caching = $state;
    }

    public function isCached($template, $cache_id = null)
    {
        $template = $this->convertTplName($template);
        return $this->engine->isCached($template, $cache_id);
    }

    /**
     * Конвертирование имен шаблонов
     * @param $name
     *
     * @return string
     * @throws Exception
     */
    public function convertTplName($name)
    {
        // Если у шаблона не указан ресурс, то выбираем между темой и системой
        $name = preg_replace('/\.'.$this->ext.'$/', '', $name);
        $name = str_replace('.', '/', $name).'.'.$this->ext;

        if (0 === strpos($name, 'theme:')) {
            $name = str_replace('theme:', '', $name);
        }
        return $name;
    }

    /**
     * Отобразить шаблон
     * @param string $tpl
     * @param int $cache_id
     */
    public function display($tpl, $cache_id = null)
    {
        $start  = microtime(1);
        $tpl = $this->convertTplName($tpl);

        $this->engine->display($tpl, $cache_id);
        $this->log($tpl . ' ('.round( microtime(1) - $start, 3 ).' sec)', 'Display tpl');
//        print "Genegated: ".round( microtime(1) - $start, 3 );
    }

    /**
     * Получить HTML шаблона
     * @param string $tpl
     * @param int $cache_id
     * @return string
     */
    public function fetch($tpl, $cache_id = null)
    {
        $start  = microtime(1);
        $tpl    = $this->convertTplName($tpl);
//        $this->engine->assign('template', $tpl);
//        $result = $this->engine->fetch('form.tpl', $cache_id);
        $result = $this->engine->fetch($tpl, $cache_id);
        $this->log($tpl . ' ('.round( microtime(1) - $start, 3).' sec)', 'Fetch tpl');
        return $result;
    }

    /**
     * Проверяет существование шаблона темы
     * @param  $tpl_name
     * @return bool
     */
    public function theme_exists($tpl_name)
    {
        $theme = $this->config['theme'];
        $path = 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'templates';

        if ( file_exists( $path.DIRECTORY_SEPARATOR.$tpl_name ) ) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет существование системного шаблона
     * @param  $tpl_name
     * @return bool
     */
    public function system_exists( $tpl_name )
    {
        $path = $this->config['admin'];
        if ( file_exists( $path.DIRECTORY_SEPARATOR.$tpl_name ) ) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет, кэшированный ли шаблон
     * @param  $tpl_file
     * @param  $cache_id
     * @param  $compile_id
     * @return mixed
     */
    public function is_cached( $tpl_file, $cache_id = null, $compile_id = null )
    {
        return $this->engine->is_cached( $tpl_file, $cache_id, $compile_id );
    }

    /**
     * Установить каталог шаблонов
     * @param $dir
     */
    public function setTplDir( $dir )
    {
        $this->engine->setTemplateDir($dir);
    }

    public function addTplDir($dir)
    {
        $this->engine->addTemplateDir($dir);
    }

    /**
     * Вывести каталог шаблонов
     *
     * @return array
     */
    public function getTplDir()
    {
        return $this->engine->getTemplateDir();
    }

    /**
     * Установить каталог скомпилированных шаблонов
     * @param $dir
     */
    public function setCplDir( $dir )
    {
        $this->engine->setCompileDir($dir);
    }

    /**
     * Каталог кэширования
     * @param  $dir
     * @return void
     */
    public function setCacheDir( $dir )
    {
        $this->engine->setCacheDir($dir);
    }

    /**
     * Установить каталог плагинов
     * @param $dir
     */
    public function addWidgetsDir( $dir )
    {
        $this->engine->addPluginsDir($dir);
    }
}
