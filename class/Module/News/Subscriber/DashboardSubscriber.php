<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\News\Subscriber;

use Module\Dashboard\Event\DashboardEvent;
use Module\News\Model\CategoryModel;
use Module\News\Model\NewsModel;
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
            DashboardEvent::EVENT_BUILD => array('onDashBuild', 100),
        );
    }

    public function onDashBuild(DashboardEvent $event)
    {
        /** @var NewsModel $modelNews */
        $modelNews = $this->dataManager->getModel('News.News');
        $newsQty = $modelNews->count('deleted = 0');

        /** @var CategoryModel $modelCat */
        $modelCat = $this->dataManager->getModel('News.NewsCategory');
        $catQty = $modelCat->count('deleted = 0');

        $latestNews = $modelNews->findAll('`deleted` = 0 AND `date` > UNIX_TIMESTAMP(NOW() - INTERVAL 1 MONTH - INTERVAL 5 DAY)', [], '`date` DESC');

        $this->tpl->assign(array(
                'newsQty' => $newsQty,
                'catQty' => $catQty,
                'latestNews' => $latestNews,
            ));
        $event->setPanel('news', 'Новости', $this->tpl->fetch('news.dashboard'));
    }
}
