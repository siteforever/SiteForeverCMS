<?php
/**
 * @author Keltanas
 */
namespace Sfcms;

class Session extends Component
{
    public function __construct()
    {
        session_start();
        $this->data = &$_SESSION;
    }

    public function __destruct()
    {
//        $_SESSION = $this->data;
    }
}
