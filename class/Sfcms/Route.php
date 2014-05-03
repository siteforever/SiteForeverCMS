<?php
/**
 * Route interface
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

use Module\System\Event\RouteEvent;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router as SfRouter;

abstract class Route
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @abstract
     */
    abstract public function route(RouteEvent $event);

    /**
     * @param \Sfcms\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Sfcms\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Из массива [ id, 10, page, 5 ] создаст параметры [ id: 10, page: 5 ]
     *
     * @param array $params
     *
     * @return array
     */
    protected function extractAsParams($params = array())
    {
        $result = array();
        if (0 == count($params) % 2) {
            $key = '';
            foreach ($params as $i => $r) {
                if ($i % 2) {
                    $result[$key] = $r;
                } else {
                    $key = $r;
                }
            }
        }

        return $result;
    }

    /**
     * @param Request $request
     *
     * @return SfRouter
     */
    protected function getSymfonyRouter(Request $request)
    {
        /** @var SfRouter $router */
        $router = \App::cms()->getContainer()->get('symfony_router');
//        if (!$router->getContext()) {
        $context = new RequestContext();
        $context->fromRequest($request);
        $router->setContext($context);
//        }
        return $router;
    }

}
