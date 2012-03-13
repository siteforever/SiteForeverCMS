<?php
/**
 * Адаптер FirePhp под Logger
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Logger_Adapter_Firephp extends FirePHP implements Logger_Interface
{
  /**
   * Gets singleton instance of Logger_Adapter_Firephp
   *
   * @param boolean $AutoCreate
   * @return Logger_Adapter_Firephp
   */
    public static function getInstance($AutoCreate=false) {
        if($AutoCreate===true && !self::$instance) {
            self::init();
        }
        return self::$instance;
    }
}
