<?php
/**
 *
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Logger_Html implements Logger_Interface
{
    protected $list_log = array();

    public function log($message, $label = '')
    {
        $this->list_log[]  = "<strong>$label</strong><br />".nl2br( $message );
    }

    function __destruct()
    {
        print "<div class='siteforever_logger'><p>".join("</p>\n<p>", $this->list_log)."</p></div>";
    }
}
