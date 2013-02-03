<?php
/**
 * @author Keltanas
 */
namespace Sfcms;

class Session extends Component
{
    public function __construct()
    {
        // Проверка, что запуск произошел через HTTP
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && in_array($_SERVER['REQUEST_METHOD'],array('GET','POST','HEAD','PUT')) ) {
            session_start();
        }
        $this->data = &$_SESSION;
    }
}
