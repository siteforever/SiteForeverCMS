<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Market\Subscriber;


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
        $model = Model::getModel('Order');
        $modelPos = Model::getModel('OrderPosition');
        $orderQty = $model->count();

        $dateMonth = mktime(0,0,0,date('n'),1);
        $orderMonthQty = $model->count('`date` > :date', array(':date'=>$dateMonth));

        $orderSum = $model->getDB()->fetchOne(
            sprintf('SELECT SUM(p.count * p.price) FROM `%s` as o LEFT JOIN `%s` as p ON p.ord_id = o.id',
                $model->getTable(),
                $modelPos->getTable()
            )
        );

        $orderMonthSum = $model->getDB()->fetchOne(
            sprintf(join(' ', array(
                        'SELECT SUM(p.count * p.price) ',
                        'FROM `%s` as o LEFT JOIN `%s` as p ON p.ord_id = o.id',
                        'WHERE o.date > :date',
                    )),
                $model->getTable(),
                $modelPos->getTable()
            ), array(':date'=>$dateMonth)
        );

        $this->tpl->assign(array(
            'orderQty' => $orderQty,
            'orderSum' => $orderSum,
            'orderMonthQty' => $orderMonthQty,
            'orderMonthSum' => $orderMonthSum,
        ));
        $event->setPanel('market', 'Интернет магазин', $this->tpl->fetch('market.dashboard'));
    }
}
