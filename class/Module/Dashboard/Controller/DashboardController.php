<?php
/**
 * Главная страница админки
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Dashboard\Controller;

use Module\Dashboard\Event\DashboardEvent;
use Sfcms\Controller;

class DashboardController extends Controller
{
    public function access()
    {
        return array(
            USER_ADMIN    => array(
                'index', 'admin',
            ),
        );
    }


    public function indexAction()
    {
        $this->request->setTitle('dashboard');

        $event = new DashboardEvent();
        $this->app->getEventDispatcher()->dispatch(DashboardEvent::EVENT_BUILD, $event);

        return $this->render('dashboard.index', array(
                'panels' => $event->getPanels(),
            ));
    }
}
