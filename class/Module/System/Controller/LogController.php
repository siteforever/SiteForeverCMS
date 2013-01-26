<?php
namespace Module\System\Controller;

use Sfcms_Controller;
use Module\System\Model\LogModel;

/**
 * Контроллер журнала операций
 * @author: keltanas
 * @link http://siteforever.ru
 */ 
class LogController extends Sfcms_Controller
{

    /**
     * Админка с использованием jqGrid
     */
    public function adminAction()
    {
        $this->request->setTitle('Просмотр журнала изменений');
        /** @var $model LogModel */
        $model = $this->getModel('Log');
        $provider = $model->getProvider();

        return array(
            'provider'      => $provider,
        );
    }

    /**
     * Реакция на аяксовый запрос от jqGrid
     * @return string
     */
    public function gridAction()
    {
        /** @var $model LogModel */
        $model = $this->getModel('Log');
        $provider = $model->getProvider();
        return $provider->getJsonData();
    }

}
