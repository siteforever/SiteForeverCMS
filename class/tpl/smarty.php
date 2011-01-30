<?php
// класс шаблонизатора
spl_autoload_unregister(array('Loader', 'load'));
require_once 'Smarty-3.0.6'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'Smarty.class.php';
spl_autoload_register(array('Loader', 'load'));


/**
 * Драйвер для Smarty
 * @author KelTanas
 */
class TPL_Smarty extends TPL_Driver
{
	private $ext = '';
	
    function __construct()
    {
        $this->engine = new Smarty(); // link (used php5)
        $this->engine->caching = false;
        $this->engine->cache_lifetime = TPL_CACHE_LIVETIME;
        $this->ext    = App::$config->get('template.ext');
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
        $tpl = preg_replace('/\.'.$this->ext.'$/', '', $tpl);
        $tpl = str_replace('.', '/', $tpl).'.'.$this->ext;
        $tpl = $this->convertTpl($tpl);

        $this->engine->display( $tpl, $cache_id );
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
     * Получить HTML шаблона
     * @param string $tpl
     * @param int $cache_id
     */
    function fetch( $tpl, $cache_id = null )
    {
        $tpl = preg_replace('/\.'.$this->ext.'$/', '', $tpl);
        $tpl = str_replace('.', '/', $tpl).'.'.$this->ext;
        $tpl = $this->convertTpl($tpl);
        
        return $this->engine->fetch( $tpl, $cache_id );
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
        $this->engine->template_dir = $dir;
    }
    
    /**
     * Вывести каталог шаблонов
     */
    function getTplDir()
    {
        return $this->engine->template_dir;
    }
    
    /**
     * Установить каталог скомпилированных шаблонов
     * @param $dir
     */
    function setCplDir( $dir )
    {
        $this->engine->compile_dir = $dir;
    }

    /**
     * Каталог кэширования
     * @param  $dir
     * @return void
     */
    function setCacheDir( $dir )
    {
        $this->engine->cache_dir = $dir;
    }
    
    /**
     * Установить каталог плагинов
     * @param $dir
     */
    function setWidgetsDir( $dir )
    {
        $this->engine->plugins_dir[] = $dir;
    }
}