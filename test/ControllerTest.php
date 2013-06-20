<?php
/**
 * Проверка контроллера
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
use Sfcms\Controller;

class TestController extends Controller
{
    function indexAction()
    {
        return true;
    }
}

class ControllerTest extends \Sfcms\Test\WebCase
{
    public function testIndexAction()
    {
    }
}
