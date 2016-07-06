<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use Module\Page\Object\Page;
use Module\System\Event\RouteEvent;
use Sfcms\Data\DataManager;
use Sfcms\Route;
use Sfcms\Model;
use Module\Page\Model\PageModel;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class StructureRoute extends Route
{
    /** @var DataManager */
    private $dataManager;

    /**
     * @return PageModel
     */
    protected function getPageModel()
    {
        return $this->getDataManager()->getModel('Page');
    }

    /**
     * @param \Sfcms\Data\DataManager $dataManager
     */
    public function setDataManager($dataManager)
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
     * Do routing
     */
    public function route(RouteEvent $event)
    {
        $router = $this->getSymfonyRouter($event->getRequest());
        $this->getPageModel()->fillRoutes($router->getRouteCollection());

        try {
            $match = $router->match($event->getRoute());
            $match['_route'] = str_replace('_alias_', '', $match['_route']);
            foreach ($match as $param => $value) {
                $event->getRequest()->set($param, $value);
            }
            $this->getLogger()->info('Match route', $match);
            /** @var Page $page */
            $page = $this->getPageModel()->findByPk($match['_id']);
            $page->setActive(1);

            $event->setRouted(true);
            $event->stopPropagation();
        } catch (ResourceNotFoundException $e) {
        }
    }
}
