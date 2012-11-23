<?php
/**
 * Класс настроек модулей сайта
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Settings
{
    /**
     * Список настроек
     * @var array|string
     */
    private $_data = array();

    /**
     * @var Model_Settings
     */
    private $_model;

    /**
     * Создает объект
     */
    public function __construct()
    {
        $modules    = App::getInstance()->getModules();
        $this->_model   = App::getInstance()->getModel('Settings');

        /** @param $module Application_Module */
        foreach ( $modules as $module ) {
            foreach ( $module->getSettings() as $key => $val ) {
                $this->set( $module->name, $key, $val );
            }
        }

        $all_data   = $this->_model->findAll();

        if ( $all_data ) {
            foreach ( $all_data as $data ) {
                $this->set( $data->module, $data->property, $data->value );
            }
        }
    }

    /**
     * Вернет все текущие настройки
     * @return array|string
     */
    public function getAll()
    {
        return $this->_data;
    }

    /**
     * Вернет список настроек для модуля
     * @param string $module
     * @return array
     */
    public function getAllForModule( $module )
    {
        if ( isset( $this->_data[ $module ] ) ) {
            return $this->_data[ $module ];
        }
        return null;
    }

    /**
     * Вернет значение свойства
     * @param string $module
     * @param string $key
     * @return null|string
     */
    public function get( $module, $key )
    {
        if ( isset ( $this->_data[ $module ][ $key ] ) ) {
            return $this->_data[ $module ][ $key ];
        }
        return null;
    }

    /**
     * Установить значение
     * @param  $module
     * @param  $key
     * @param  $value
     * @return void
     */
    public function set( $module, $key, $value )
    {
        $this->_data[ (string) $module ][ (string) $key ] = $value;
    }
}
