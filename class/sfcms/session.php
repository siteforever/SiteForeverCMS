<?php
/**
 * @author Keltanas
 */
namespace Sfcms;

class Session extends Component
{
    public function __construct()
    {
        $this->data = $_SESSION;
    }

    public function __destruct()
    {
        $_SESSION = $this->data;
    }
}
