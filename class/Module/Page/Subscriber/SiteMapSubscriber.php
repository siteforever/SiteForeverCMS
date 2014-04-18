<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Page\Subscriber;


use Module\Page\Component\SiteMap\SiteMapItem;
use Module\Page\Component\SiteMap\SiteMapSubscriberAbstract;
use Module\Page\Event\SiteMapEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteMapSubscriber extends SiteMapSubscriberAbstract
{
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
            SiteMapEvent::EVENT_CONSTRUCT => 'onSiteMap',
        );
    }

    public function onSiteMap(SiteMapEvent $event)
    {
        $pages = $this->dataManager->getModel('Page')->getAll();
        $host = $event->getRequest()->getSchemeAndHttpHost();
        foreach ($pages as $page) {
            $item = new SiteMapItem($host . ('index' == $page->alias ? '' : '/' . $page->alias));
            $item->setLastmod($page->update);
            $event->getMap()->add($item);
        }
    }
}
