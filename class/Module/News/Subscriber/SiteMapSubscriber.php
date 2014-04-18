<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\News\Subscriber;


use Module\News\Model\NewsModel;
use Module\News\Object\News;
use Module\Page\Component\SiteMap\SiteMapItem;
use Module\Page\Event\SiteMapEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteMapSubscriber implements EventSubscriberInterface
{
    /** @var  NewsModel */
    private $model;

    function __construct(NewsModel $model)
    {
        $this->model = $model;
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
            SiteMapEvent::EVENT_CONSTRUCT => 'onSiteMap',
        );
    }

    public function onSiteMap(SiteMapEvent $event)
    {
        /** @var News[] $news */
        $news = $this->model->findAll('`hidden` = 0 AND `protected` = 0 AND `deleted` = 0');
        $host = $event->getRequest()->getSchemeAndHttpHost();
        foreach ($news as $new) {
            $item = new SiteMapItem($host . '/' . $new->getUrl());
            $item->setLastmod($new->date);
            $event->getMap()->add($item);
        }
    }
}
