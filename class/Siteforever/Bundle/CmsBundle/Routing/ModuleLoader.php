<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Siteforever\Bundle\CmsBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class ModuleLoader extends Loader
{
    /** @var \App */
    protected $kernel;

    function __construct(\App $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string $type The resource type
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        foreach ($this->kernel->getModules() as $module) {
            $moduleRoutes = $module->registerRoutes();
            foreach ($moduleRoutes as $name => $route) {
                $defaults = $route->getDefaults();
                if (isset($defaults['_controller'])) {
                    $defaults['controller'] = $defaults['_controller'];
                }
                $defaults['_controller'] = 'SiteforeverCmsBundle:Default:index';
                if (isset($defaults['_action'])) {
                    $defaults['action'] = $defaults['_action'];
                    unset($defaults['_action']);
                }
                $route->setDefaults($defaults);
            }
            $collection->addCollection($moduleRoutes);
        }

        return $collection;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string $type The resource type
     *
     * @return bool    true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'sfcms_modules' == $type;
    }
}
