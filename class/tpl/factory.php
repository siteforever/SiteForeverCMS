<?php
/**
 * Стратегия выбора шаблонизатора
 * @author KelTanas
 */

class Tpl_Exception extends Exception {};

class Tpl_Factory
{
    public  static $template_dir;
    public  static $compile_dir;
    public  static $config_dir;
    public  static $cache_dir;
    
    /**
     * Вернет инстанс шаблонизатора
     * @return TPL_Smarty
     */
    static function create( Application_Abstract $app )
    {
        $cfg = $app->getConfig()->get('template');

        if ( ! $cfg ) {
            throw new Tpl_Exception('Config for templates not defined');
        }
        $driver = $cfg['driver'];
        $theme  = $cfg['theme'];

        /**
         * @var TPL_Driver $obj
         */
        if ( class_exists( $driver ) ) {
            $obj = new $driver();
            //Register::setTpl( $obj );
            $obj->setTplDir(ROOT."/themes/{$theme}/templates");
            $runtime    = ROOT."/protected/_runtime";
            $tpl_c  = $runtime."/_templates_c";
            $cache  = $runtime."/_cache";

            if ( ! is_dir( $tpl_c ) ) {
                @mkdir( $tpl_c, 0755, true );
            }
            if ( ! is_dir( $cache ) ) {
                @mkdir( $cache, 0755, true );
            }
            $obj->setCplDir($tpl_c);
            $obj->setCacheDir($cache);

            $obj->setWidgetsDir( SF_PATH.'/widgets' );
            if ( ROOT != SF_PATH ) {
                $obj->setWidgetsDir( ROOT.'/widgets' );
            }
            return $obj;
        }
        else {
            throw new Exception("Templates driver '{$driver}' not found");
        }
    }
}