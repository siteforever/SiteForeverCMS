<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Subscriber;

use Module\System\Event\ControllerEvent;
use Sfcms\Data\DataManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ControllerSubscriber implements EventSubscriberInterface
{
    /** @var DataManager */
    private $dataManager;

    function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     * @return \Sfcms\Data\DataManager
     */
    public function getDataManager()
    {
        return $this->dataManager;
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
        return [
            ControllerEvent::RUN_BEFORE => 'onBefore',
            ControllerEvent::RUN_AFTER => 'onAfter',
        ];
    }

    public function onBefore(ControllerEvent $event)
    {
        $request    = $event->getRequest();
        $pageId     = $request->get('_id', 0);
        $controller = $request->getController();
        $action     = $request->getAction();

        // Define page
        $pageObj = null;
        if ($controller) {
            if ($pageId && 'index' == $action) {
                $model    = $this->getDataManager()->getModel('Page');
                $pageObj = $model->find($pageId);
            }
        }

        if (null !== $pageObj) {
            // Если страница указана как объект, то в нее нельзя сохранять левые данные
            $request->setTemplate($pageObj->get('template'));
            $request->setTitle($pageObj->get('title'));
            $request->setDescription($pageObj->get('description'));
            $request->setKeywords($pageObj->get('keywords'));
        }

        $event->getController()->setPage($pageObj);
    }

    public function onAfter($event)
    {

    }
}
