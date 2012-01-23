<?php
/**
 * Логер
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class std_logger implements std_logger_logger
{
    static private $_instance   = null;

    private $_logger    = null;

    /**
     * @param std_logger_logger $logger
     */
    private function __construct( std_logger_logger $logger )
    {
        $this->_logger  = $logger;
    }

    /**
     * @static
     * @param std_logger_logger $logger
     * @return self
     */
    public static function getInstance( std_logger_logger $logger = null )
    {
        if ( null === self::$_instance ) {
            if ( null === $logger ) {
                $logger = new std_logger_blank();
            }
            self::$_instance    = new std_logger( $logger );
        }
        return self::$_instance;
    }


    /**
     * @param $message
     * @param $label
     * @return mixed
     */
    public function log( $message, $label = '' )
    {
        if ( $this->_logger ) {
            return $this->_logger->log( $message, $label );
        }
    }
}
