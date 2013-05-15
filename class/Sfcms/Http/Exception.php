<?php
/**
 * Ошибка протокола HTTP
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
use Symfony\Component\HttpKernel\Exception\HttpException;

class Sfcms_Http_Exception extends HttpException
{
    public function __construct($message, $code = 500)
    {
        parent::__construct($code, $message, null, array(), $code);
    }
}
