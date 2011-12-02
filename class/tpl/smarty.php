<?php
// класс шаблонизатора
App::autoloadUnRegister(array('App', 'autoload'));
require_once 'Smarty-3.1.5'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'Smarty.class.php';
App::autoloadRegister(array('App','autoload'));

/**
 * Драйвер для Smarty
 * @author KelTanas
 */
class TPL_Smarty extends TPL_Driver
{
    private $ext = '';

    function __construct()
    {
        $config    = App::getInstance()->getConfig();

        $this->engine = new Smarty(); // link (used php5)
        $this->engine->caching = false;
        $this->engine->cache_lifetime = $config->get('template.cache.livetime');
        $this->ext    = $config->get('template.ext');
    }
    
    function assign( $params, $value = null )
    {
        $this->engine->assign( $params, $value );
    }

    /**
     * Установка кэширования
     * @param bool $state
     * @return void
     */
    function caching( $state = false )
    {
        $this->engine->caching = $state;
    }

    /**
     * Конвертирование шаблонов
     * @param String $tpl
     * @return string
     */
    function convertTpl( $tpl )
    {
        // Если у шаблона не указан ресурс, то выбираем между темой и системой
        if ( ! preg_match('/(\w+):(.*)/', $tpl, $m) ) {
            if ( $this->theme_exists( $tpl ) ) {
                $tpl    = 'theme:'.$tpl;
            }
            elseif ( $this->system_exists( $tpl ) ) {
                $tpl    = 'system:'.$tpl;
            }
            else {
                die('Шаблон '.$tpl.' не найден');
            }
        }
        return $tpl;
    }
    
    /**
     * Отобразить шаблон
     * @param string $tpl
     * @param int $cache_id
     */
    function display( $tpl, $cache_id = null )
    {
        $start  = microtime(1);
        $tpl = preg_replace('/\.'.$this->ext.'$/', '', $tpl);
        $tpl = str_replace('.', '/', $tpl).'.'.$this->ext;
        $tpl = $this->convertTpl($tpl);

        $this->engine->display( $tpl, $cache_id );
//        print "Genegated: ".round( microtime(1) - $start, 3 );
    }

    /**
     * Получить HTML шаблона
     * @param string $tpl
     * @param int $cache_id
     */
    function fetch( $tpl, $cache_id = null )
    {
//        $start  = microtime(1);
        $tpl    = preg_replace('/\.'.$this->ext.'$/', '', $tpl);
        $tpl    = str_replace('.', '/', $tpl).'.'.$this->ext;
        $tpl    = $this->convertTpl($tpl);

        $result = $this->engine->fetch( $tpl, $cache_id );
//        print "Genegated: ".round( microtime(1) - $start, 3 );
        return $result;
    }

    /**
     * Проверяет существование шаблона темы
     * @param  $tpl_name
     * @return bool
     */
    function theme_exists( $tpl_name )
    {
        $theme = App::$config->get('template.theme');
        $path = 'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'templates';

        //print $path.DIRECTORY_SEPARATOR.$tpl_name;

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
    function system_exists( $tpl_name )
    {
        $path = App::$config->get('template.admin');
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
     * @return false|string
     */
    function is_cached( $tpl_file, $cache_id = null, $compile_id = null )
    {
        return $this->engine->is_cached( $tpl_file, $cache_id, $compile_id );
    }

    /**
     * Установить каталог шаблонов
     * @param $dir
     */
    function setTplDir( $dir )
    {
        $this->engine->setTemplateDir($dir);
    }
    
    /**
     * Вывести каталог шаблонов
     */
    function getTplDir()
    {
        return $this->engine->getTemplateDir(0);
    }
    
    /**
     * Установить каталог скомпилированных шаблонов
     * @param $dir
     */
    function setCplDir( $dir )
    {
        $this->engine->setCompileDir($dir);
    }

    /**
     * Каталог кэширования
     * @param  $dir
     * @return void
     */
    function setCacheDir( $dir )
    {
        $this->engine->setCacheDir($dir);
    }
    
    /**
     * Установить каталог плагинов
     * @param $dir
     */
    function setWidgetsDir( $dir )
    {
        $this->engine->addPluginsDir($dir);
    }
}