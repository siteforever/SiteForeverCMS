<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
class Controller_System extends Sfcms_Controller
{
    function indexAction()
    {
        $this->request->setTitle('Конфигурация системы');
        $this->request->set('tpldata.page.template', 'index');

        $modules = get_loaded_extensions();

        $this->app()->getLogger()->log( $modules );

        $msys   = array();

        foreach( $modules as $i => $module ) {
            if ( $msys[$i] = @ini_get_all($module, false) ) {

            } else {
                unset( $modules[$i] );
            }
        }

        $sys = ini_get_all(null, false);

        $this->tpl->assign(
            array(
                'sys'     => $sys,
                'msys'    => $msys,
                'modules' => $modules,
                'ver'     => phpversion(),
            )
        );

        $this->request->setContent( $this->tpl->fetch('system:system.index') );
    }
}
