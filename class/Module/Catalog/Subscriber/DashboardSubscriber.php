<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Subscriber;

use Module\Catalog\Model\CatalogModel;
use Module\Dashboard\Event\DashboardEvent;
use Sfcms\Tpl\Driver;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DashboardSubscriber implements EventSubscriberInterface
{
    use ContainerAwareTrait;

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

    /**
     * @return Driver
     */
    public function getTpl()
    {
        return $this->container->get('tpl');
    }

    public function onDashBuild(DashboardEvent $event)
    {
        /** @var CatalogModel $model */
        $model = $this->container->get('data.manager')->getModel('catalog');
        $productQty = $model->count('cat = 0 AND deleted = 0');
        $productAbsentQty = $model->count('absent = 1 AND deleted = 0');
        $productHiddenQty = $model->count('hidden = 1 AND deleted = 0');
        $productDeletedQty = $model->count('deleted = 1');

        $this->getTpl()->assign(array(
                'productQty' => $productQty,
                'productAbsentQty' => $productAbsentQty,
                'productHiddenQty' => $productHiddenQty,
                'productDeletedQty' => $productDeletedQty,
            ));
        $event->setPanel('catalog', 'Каталог', $this->getTpl()->fetch('catalog.dashboard'));
    }
}
