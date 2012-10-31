<?php
/**
 * Контроллер управления настройками
 * @author keltanas
 */
class Controller_Setting extends Sfcms_Controller
{

    /**
     * Действие по умолчанию
     * @return array
     */
    public function adminAction()
    {
        $this->request->setTitle(t('Settings'));

        return array(
            'modules'   => $this->app()->getModules(),
            'settings'  => $this->app()->getSettings()->getAll(),
        );
    }

    /**
     * Сохранение данных
     * @return mixed
     */
    public function saveAction()
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

        return t('Settings saved');
    }

    /**
     * Уровень доступа
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array('admin', 'save'),
        );
    }
}