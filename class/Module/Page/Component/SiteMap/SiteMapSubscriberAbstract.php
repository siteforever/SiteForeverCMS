<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Page\Component\SiteMap;

use Sfcms\Data\DataManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class SiteMapSubscriberAbstract implements EventSubscriberInterface
{
    /** @var DataManager */
    protected $dataManager;

    function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }
}
