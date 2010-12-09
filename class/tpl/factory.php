<?php
/**
 * Стратегия выбора шаблонизатора
 * @author KelTanas
 */
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
    static function create()
    {
        $cfg = App::$config->get('template');
        $driver = $cfg['driver'];
        $theme  = $cfg['theme'];

        if ( class_exists( $driver ) ) {
            $obj = new $driver();
            //Register::setTpl( $obj );

            $obj->setTplDir("themes/{$theme}/templates");

            $tpl_c  = "protected/_runtime/_templates_c";
            $cache  = "protected/_runtime/_cache";

            if ( ! is_dir( $tpl_c ) ) {
                mkdir( $tpl_c, 0666, true );
            }
            if ( ! is_dir( $cache ) ) {
                mkdir( $cache, 0666, true );
            }
            $obj->setCplDir($tpl_c);
            $obj->setCacheDir($cache);

            $obj->setWidgetsDir( $cfg['widgets'] );
            return $obj;
        }
        else {
            throw new Exception("Драйвер шаблонов {$driver} не найден");
        }
    }



}