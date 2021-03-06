<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
namespace Module\System\Controller;

use Sfcms\Controller;

class SystemController extends Controller
{
    public function access()
    {
        return array(
            'system' => array('index','assembly','jqgrid'),
        );
    }

    public function indexAction()
    {
        $this->request->setTitle($this->t('System configuration'));
        $this->request->setTemplate('index');
        $modules = get_loaded_extensions();
        $msys   = array();
        foreach( $modules as $i => $module ) {
            if (! ($msys[$i] = @ini_get_all($module, false))) {
                unset( $modules[$i] );
            }
        }
        $sys = ini_get_all(null, false);
        return $this->render('system.index', array(
            'sys'     => $sys,
            'msys'    => $msys,
            'modules' => $modules,
            'ver'     => phpversion(),
        ));
    }


    /**
     * Сборка
     */
    public function assemblyAction()
    {
        $this->request->setTitle($this->t('Assembly'));
    }

}
