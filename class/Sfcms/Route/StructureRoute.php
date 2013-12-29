<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use App;
use Module\Page\Object\Page;
use Module\System\Event\RouteEvent;
use Sfcms\Request;
use Sfcms\Route;
use Sfcms\Module;
use Sfcms\Model;
use Module\Page\Model\PageModel;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class StructureRoute extends Route
{
    /**
     * @return PageModel
     */
    protected function getPageModel()
    {
        return Model::getModel('Page');
    }

    /**
     * Do routing
     */
    public function route(RouteEvent $event)
    {
        $router = $this->getSymfonyRouter($event->getRequest());
        $this->getPageModel()->fillRoutes($router);

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
