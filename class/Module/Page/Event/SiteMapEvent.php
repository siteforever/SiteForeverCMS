<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Page\Event;


use Doctrine\Common\Collections\ArrayCollection;
use Sfcms\Request;
use Symfony\Component\EventDispatcher\Event;

class SiteMapEvent extends Event
{
    const EVENT_CONSTRUCT = 'sfcms.sitemap.construct';

    /** @var ArrayCollection */
    private $map;

    /** @var Request */
    private $request;

    public function __construct(Request $request)
    {
        $this->map = new ArrayCollection();
        $this->request = $request;
    }

    /**
     * @return ArrayCollection
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @return \Sfcms\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
