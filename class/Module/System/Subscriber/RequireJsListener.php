<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Subscriber;

use Assetic\Asset\AssetCollection;
use Assetic\AssetWriter;
use Module\System\Event\StaticEvent;
use Module\System\Service\AsseticService;

/**
 * Collects in a pile require_js and depending
 *
 * @package Module\System\Subscriber
 */
class RequireJsListener
{
    /** @var AssetWriter */
    protected $assetWriter;
    /** @var AsseticService */
    protected $assetService;

    function __construct(AsseticService $assetService)
    {
        $this->assetService = $assetService;
        $this->assetWriter = new AssetWriter($assetService->getDir());
    }

    public function installRequireJs(StaticEvent $event)
    {
        /** @var AssetCollection $asset */
        $asset = $this->assetService->getAsseticCollection('require_js'); // @see Module\System\DependencyInjection\Compiler\RequireJsPass
        $this->assetWriter->writeAsset($asset);

        $event->getOutput()->writeln(sprintf('<info>Js "%s" was updated.</info>', $asset->getTargetPath()));
    }
}
