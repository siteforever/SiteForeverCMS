<?php
/**
 * Логер
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Std_Logger implements Std_Logger_Logger
{
    static private $_instance   = null;

    private $_logger    = null;

    /**
     * @param Std_Logger_Logger $logger
     */
    private function __construct( Std_Logger_Logger $logger )
    {
        $this->_logger  = $logger;
    }

    /**
     * @static
     * @param Std_Logger_Logger $logger
     * @return Std_Logger
     */
    public static function getInstance( Std_Logger_Logger $logger = null )
    {
        if ( null === self::$_instance ) {
            if ( null === $logger ) {
                $logger = new Std_Logger_Blank();
            }
            self::$_instance    = new Std_Logger( $logger );
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

    public function dump( )
    {
        if ( $this->_logger && method_exists( $this->_logger, 'dump' ) ) {
            return $this->_logger->dump( func_get_args() );
        }
    }
}
