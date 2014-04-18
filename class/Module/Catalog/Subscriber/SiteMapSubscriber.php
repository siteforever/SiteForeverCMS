<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Catalog\Subscriber;


use Module\Catalog\Model\CatalogModel;
use Module\Catalog\Object\Catalog;
use Module\Page\Component\SiteMap\SiteMapItem;
use Module\Page\Event\SiteMapEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteMapSubscriber implements EventSubscriberInterface
{
    /** @var  CatalogModel */
    private $model;

    function __construct(CatalogModel $model)
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
        /** @var Catalog[] $products */
        $products = $this->model->findAllProducts();
        $host = $event->getRequest()->getSchemeAndHttpHost();
        foreach ($products as $product) {
            $item = new SiteMapItem($host . '/' . $product->getUrl());
            $item->setLastmod(new \DateTime()); // todo need implement created_at and updated_at for catalogue
            $event->getMap()->add($item);
        }
    }
}
