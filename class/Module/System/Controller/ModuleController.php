<?php
/**
 * Контроллер, управляющий модулями
 * @author: keltanas
 * @link  http://siteforever.ru
 */
namespace Module\System\Controller;

use Sfcms_Controller;

class ModuleController extends Sfcms_Controller
{
    public function access()
    {
        return array(
            USER_ADMIN => array('admin','edit','save','grid'),
        );
    }


    public function adminAction()
    {

    }


    public function editAction( $id )
    {

    }


    public function saveAction()
    {

    }


    public function gridAction()
    {

    }
}
