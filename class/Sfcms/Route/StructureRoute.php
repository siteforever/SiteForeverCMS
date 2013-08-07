<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use App;
use Module\Page\Object\Page;
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
     * @param $request
     * @param $route
     *
     * @return bool
     */
    public function route(Request $request, $route)
    {
        $router = $this->getRouter($request);
        $this->getPageModel()->fillRoutes($router);

        try {
            $match = $router->match($route);
            $match['_route'] = str_replace('_alias_', '', $match['_route']);
            foreach ($match as $param => $value) {
                $request->set($param, $value);
            }
            App::cms()->getLogger()->info('Match route', $match);
            /** @var Page $page */
            $page = $this->getPageModel()->findByPk($match['_id']);
            $page->setActive(1);
        } catch (ResourceNotFoundException $e) {
            return false;
        }

        return true;
    }
}
