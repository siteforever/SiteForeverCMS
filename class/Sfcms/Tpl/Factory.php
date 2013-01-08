<?php
/**
 * Стратегия выбора шаблонизатора
 * @author KelTanas
 */

namespace Sfcms\Tpl;

use Sfcms\Exception;
use Sfcms\Tpl\Smarty;
use Sfcms\Tpl\Driver;
use Sfcms\Kernel\Base as Service;

class Factory
{
    public  static $template_dir;
    public  static $compile_dir;
    public  static $config_dir;
    public  static $cache_dir;

    /**
     * Вернет инстанс шаблонизатора
     * @param Service $app
     *
     * @return Driver
     * @throws Exception
     */
    static function create( Service $app )
    {
        $cfg = $app->getConfig()->get('template');

        if ( ! $cfg ) {
            throw new Exception('Config for templates not defined');
        }
        $driver = $cfg['driver'];
        $theme  = $cfg['theme'];

        /**
         * @var Driver $obj
         */
        if ( class_exists( $driver ) ) {
            $obj = new $driver();
            //Register::setTpl( $obj );
            $themeCat = ROOT."/themes/{$theme}/templates";
            if ( is_dir( $themeCat ) ) {
                $obj->setTplDir( $themeCat );
            } else {
                throw new Exception( 'Theme "'.$theme.'" not found' );
            }
            $runtime    = ROOT."/_runtime";
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