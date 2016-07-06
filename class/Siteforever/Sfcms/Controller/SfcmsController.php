<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Siteforever\Sfcms\Controller;

use Sfcms\Auth;
use Sfcms\Controller\Resolver;
use Sfcms\Tpl\Driver;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SfcmsController extends Controller
{
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Auth
     */
    protected function getAuth()
    {
        return $this->container->get('auth');
    }

    /**
     * @return Driver
     */
    protected function getTpl()
    {
        return $this->container->get('tpl');
    }

    /**
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->container->get('sfcms.resolver');
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }
}
