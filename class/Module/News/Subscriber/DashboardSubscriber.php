<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\News\Subscriber;

use Module\Dashboard\Event\DashboardEvent;
use Sfcms\Model;
use Sfcms\Tpl\Driver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DashboardSubscriber implements EventSubscriberInterface
{
    /** @var  Driver */
    private $tpl;

    public function __construct(Driver $tpl)
    {
        $this->tpl = $tpl;
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
            DashboardEvent::EVENT_BUILD => array('onDashBuild', 100),
        );
    }

    public function onDashBuild(DashboardEvent $event)
    {
        $modelNews = Model::getModel('News');
        $newsQty = $modelNews->count('deleted = 0');

        $modelCat = Model::getModel('NewsCategory');
        $catQty = $modelCat->count('deleted = 0');

        $this->tpl->assign(array(
                'newsQty' => $newsQty,
                'catQty' => $catQty,
            ));
        $event->setPanel('news', 'Новости', $this->tpl->fetch('news.dashboard'));
    }
}
