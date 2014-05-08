<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\JqGrid\Listener;

use Assetic\AssetWriter;
use Module\System\Event\StaticEvent;
use Module\System\Service\AsseticService;

class StaticListener
{
    /** @var AsseticService */
    private $asseticService;

    /** @var string */
    private $rootDir;

    public function __construct(AsseticService $asseticService, $rootDir)
    {
        $this->asseticService = $asseticService;
        $this->rootDir = $rootDir;
    }

    public function installJqGrid(StaticEvent $event)
    {
        $jsAsset = $this->asseticService->getAsseticCollection('jqgrid_js');
        $writer = new AssetWriter($event->getStaticDir());
        $writer->writeAsset($jsAsset);

        $cssAsset = $this->asseticService->getAsseticCollection('jqgrid_css');
        $writer->writeAsset($cssAsset);

        $event->getOutput()->writeln(sprintf('<info>Js "%s" was updated.</info>', $jsAsset->getTargetPath()));
        $event->getOutput()->writeln(sprintf('<info>Css "%s" was updated</info>', $cssAsset->getTargetPath()));
    }
}
