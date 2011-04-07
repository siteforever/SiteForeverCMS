<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
class Controller_System extends Controller
{
    function indexAction()
    {
        $this->request->setTitle('Конфигурация системы');
        $this->request->set('tpldata.page.template', 'index');

        $modules    = array(
            'mbstring',
            'pdo_mysql',
            'iconv',
            'pcre',
            'date',
            'apc',
            'xdebug',
            'zend',
            'zlib',
        );

        $common = array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        );

        $msys   = array();

        foreach( $modules as $i => $module ) {
            if ( $msys[$i] = @ini_get_all($module, false) ) {

            } else {
                unset( $modules[$i] );
            }
        }

        //printVar($msys);

        $sys = ini_get_all(null, false);

        /*foreach( $sys as $key => $value ) {
            if ( strpos( $key, '.' ) ) {
                unset( $sys[ $key ] );
                $key = str_replace('.', '_', $key);
                $sys[ $key ] = $value;
            }
        }*/

        $this->tpl->sys     = $sys;
        $this->tpl->msys    = $msys;
        $this->tpl->modules = $modules;
        $this->tpl->ver = phpversion();

        $this->request->setContent( $this->tpl->fetch('system:system.index') );
    }
}
