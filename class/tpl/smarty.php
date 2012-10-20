<?php
/**
 * Драйвер для Smarty
 * @author KelTanas
 */
class TPL_Smarty extends TPL_Driver
{
    private $ext = '';

    private $config = array();

    public function __construct()
    {
        $this->app()->getConfig()->setDefault('template', array(
            'admin'     => SF_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'system',
            'widgets'   => SF_PATH.DIRECTORY_SEPARATOR.'widgets',
            'ext'       => 'tpl',
            'compile_check' => true,
            'caching'   => false,
            'version'   => '3.1.11',
            'cache'     => array(
                'livetime' => 84600,
            ),
        ));
        $this->config = $this->app()->getConfig('template');

        // класс шаблонизатора
        $ver = $this->config['version'];
        if( ! $ver ) throw new RuntimeException(t('Smarty version not defined'));

        App::autoloadUnRegister(array('App', 'autoload'));
        require_once 'Smarty-'.$ver.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'Smarty.class.php';
        App::autoloadRegister(array('App','autoload'));

        $this->engine = new Smarty(); // link (used php5)
        $this->engine->cache_lifetime = $this->config['cache']['livetime'];
        $this->ext    = $this->config['ext'];

        $this->engine->compile_check = $this->config['compile_check'];
        $this->engine->caching = $this->config['caching'];
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
    public function caching( $state = false )
    {
        $this->engine->caching = $state;
    }

    /**
     * Конвертирование шаблонов
     * @param $tpl
     *
     * @return string
     * @throws Exception
     */
    public function convertTplName( $tpl )
    {
        // Если у шаблона не указан ресурс, то выбираем между темой и системой
        if ( ! preg_match('/(\w+):(.*)/', $tpl, $m) ) {
            if ( $this->theme_exists( $tpl ) ) {
                $tpl    = 'theme:'.$tpl;
            } elseif ( $this->system_exists( $tpl ) ) {
                $tpl    = 'system:'.$tpl;
            } else {
                throw new Exception('Шаблон '.$tpl.' не найден');
            }
        }
        return $tpl;
    }
    
    /**
     * Отобразить шаблон
     * @param string $tpl
     * @param int $cache_id
     */
    public function display( $tpl, $cache_id = null )
    {
        $start  = microtime(1);
        $tpl = preg_replace('/\.'.$this->ext.'$/', '', $tpl);
        $tpl = str_replace('.', '/', $tpl).'.'.$this->ext;
        $tpl = $this->convertTplName($tpl);

        $this->engine->display( $tpl, $cache_id );
        $this->log($tpl . ' ('.round( microtime(1) - $start, 3 ).' sec)', 'Display tpl');
//        print "Genegated: ".round( microtime(1) - $start, 3 );
    }

    /**
     * Получить HTML шаблона
     * @param string $tpl
     * @param int $cache_id
     * @return string
     */
    public function fetch( $tpl, $cache_id = null )
    {
        $start  = microtime(1);
        $tpl    = preg_replace('/\.'.$this->ext.'$/', '', $tpl);
        $tpl    = str_replace('.', '/', $tpl).'.'.$this->ext;
        $tpl    = $this->convertTplName($tpl);

        $result = $this->engine->fetch( $tpl, $cache_id );
        $this->log($tpl . ' ('.round( microtime(1) - $start, 3 ).' sec)', 'Fetch tpl');
        return $result;
    }

    /**
     * Проверяет существование шаблона темы
     * @param  $tpl_name
     * @return bool
     */
    public function theme_exists( $tpl_name )
    {
        $theme = $this->app()->getConfig()->get('template.theme');
        $path = 'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'templates';

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
        $path = $this->app()->getConfig()->get('template.admin');
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
    
    /**
     * Вывести каталог шаблонов
     */
    public function getTplDir()
    {
        return $this->engine->getTemplateDir(0);
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
    public function setWidgetsDir( $dir )
    {
        $this->engine->addPluginsDir($dir);
    }
}