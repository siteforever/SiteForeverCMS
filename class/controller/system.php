<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
class controller_System extends Controller
{
    function indexAction()
    {
        $this->request->setTitle('Конфигурация системы');
        $this->request->set('tpldata.page.template', 'index');

        $sys = ini_get_all();
        
        foreach( $sys as $key => $value ) {
            if ( strpos( $key, '.' ) ) {
                unset( $sys[ $key ] );
                $key = str_replace('.', '_', $key);
                $sys[ $key ] = $value;
            }
        }

        $this->tpl->sys = $sys;

        $this->request->setContent( $this->tpl->fetch('system:system.index') );
    }
}