<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
class Controller_System extends Sfcms_Controller
{
    public function access()
    {
        return array(
            'system' => array('index','assembly','jqgrid'),
        );
    }

    public function indexAction()
    {
        $this->request->setTitle('Конфигурация системы');
        $this->request->setTemplate('index');

        $modules = get_loaded_extensions();

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


    /**
     * Сборка
     */
    public function assemblyAction()
    {
        $this->request->setTitle(t('Assembly'));
    }

}
