<?php
/**
 * Контроллер управления настройками
 * @author keltanas
 */
class Controller_Settings extends Sfcms_Controller
{

    /**
     * Действие по умолчанию
     * @return void
     */
    function indexAction()
    {
        $this->request->setTitle(t('Settings'));

        $this->tpl->modules = $this->app()->getModules();
        $this->tpl->settings= $this->app()->getSettings()->getAll();

        $this->request->setContent( $this->tpl->fetch('system:settings.admin') );
    }

    /**
     * Сохранение данных
     * @return void
     */
    function saveAction()
    {
        $model      = $this->getModel('Settings');

        $settings   = $this->app()->getSettings();

        $modules    = $this->app()->getModules();
        foreach ( $modules as $module ) {
            if ( $values = $this->request->get($module->name) ) {
                foreach ( $values as $key => $val ) {
                    $settings->set( $module->name, $key, $val );

                    $this->getDB()->insertUpdate( $model->getTableName(), array(
                        'module'    => $module->name,
                        'property'  => $key,
                        'value'     => $val,
                    ));
                }
            }
        }

        print t('Settings saved');
    }

    /**
     * Уровень доступа
     * @return array
     */
    function access()
    {
        return array(
            'system'    => array('index', 'save'),
        );
    }
}