<?php
namespace Module\System\Controller;

use Sfcms\Controller;
use Module\System\Model\LogModel;

/**
 * Контроллер журнала операций
 * @author: keltanas
 * @link http://siteforever.ru
 */
class LogController extends Controller
{
    public function access()
    {
        return array(
            USER_ADMIN => array('admin', 'grid'),
        );
    }

    /**
     * Админка с использованием jqGrid
     */
    public function adminAction()
    {
        $this->request->setTitle('Просмотр журнала изменений');
        /** @var $model LogModel */
        $model = $this->getModel('Log');
        $provider = $model->getProvider($this->request);

        return array(
            'provider' => $provider,
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
        $provider = $model->getProvider($this->request);
        return $provider->getJsonData();
    }
}
