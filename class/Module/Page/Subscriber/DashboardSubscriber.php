<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Subscriber;


use Module\Dashboard\Event\DashboardEvent;
use Module\Page\Model\PageModel;
use Sfcms\Data\DataManager;
use Sfcms\Model;
use Sfcms\Tpl\Driver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DashboardSubscriber implements EventSubscriberInterface
{
    /** @var  Driver */
    private $tpl;

    /** @var DataManager */
    private $dataManager;

    public function __construct(Driver $tpl, DataManager $dataManager)
    {
        $this->tpl = $tpl;
        $this->dataManager = $dataManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            DashboardEvent::EVENT_BUILD => array('onDashBuild', 1000),
        );
    }

    public function onDashBuild(DashboardEvent $event)
    {
        /** @var PageModel $model */
        $model = $this->dataManager->getModel('page');
        $pageQty = $model->count('deleted = 0');
        $pageHiddenQty = $model->count('hidden = 1 AND deleted = 0');
        $pageDeletedQty = $model->count('deleted = 1');

        $latestPages = $model->findAll('`deleted` = 0 AND `update` > UNIX_TIMESTAMP(NOW() - INTERVAL 1 MONTH - INTERVAL 5 DAY)', [], '`update` DESC');

        $this->tpl->assign(array(
            'pageQty' => $pageQty,
            'pageHiddenQty' => $pageHiddenQty,
            'pageDeletedQty' => $pageDeletedQty,
            'latestPages' => $latestPages,
        ));
        $event->setPanel('pages', 'Структура', $this->tpl->fetch('page.dashboard'));
    }
}
