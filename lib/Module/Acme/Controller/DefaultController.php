<?php
/**
 * Проверочный класс
 * @author: keltanas
 */
namespace Module\Acme\Controller;

use Sfcms_Controller;

class DefaultController extends Sfcms_Controller
{
    public function indexAction()
    {
        return array('name'=>'KelTanas');
    }
}