<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use App;
use Module\Page\Object\Page;
use Sfcms\Route;
use Sfcms\Module;
use Sfcms\Model;
use Module\Page\Model\PageModel;

class StructureRoute extends Route
{
    protected static $_aliases = null;

    /**
     * Заполняет таблицу алиасов, если она отсутствует
     */
    protected function callAliases()
    {
        if (null === self::$_aliases) {
            /** @var $model PageModel */
            $model = Model::getModel('Page');
            foreach ($model->getAll() as $page) {
                self::$_aliases[$page->alias] = $page;
            }
        }
    }

    /**
     * @param $route
     * @return mixed
     */
    public function route($route)
    {
        $alias = null;
        $this->callAliases();
        /** @var $page Page */
        do {
            if (isset(self::$_aliases[$route])) {
                if (null !== $alias) {
                    $this->request->attributes->set('alias', $alias);
                }
                /*$route->setActive(1);*/
                call_user_func(array(self::$_aliases[$route], 'setActive'), 1);
                $this->request->query->set('route', $route);
                return $this->getPageState(self::$_aliases[$route]);
            }
            $arRoute = explode('/', $route);
            $alias   = array_pop($arRoute);
            $route   = implode('/', $arRoute);
        } while ($route);

        return false;
    }

    /**
     * @param Page $page
     *
     * @return array
     */
    private function getPageState(Page $page)
    {
        return array(
            'controller' => $page->controller,
            'action'     => $page->action,
            'params'     => array(
                'template' => $page->template,
                'pageid'   => $page->id,
                'system'   => $page->system,
            ),
        );
    }

}
