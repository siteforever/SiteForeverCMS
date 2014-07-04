<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Subscriber;

use Assetic\AssetManager;
use Assetic\AssetWriter;

class AssetWriteListener
{
    /** @var AssetManager */
    private $manager;

    private $dir;

    /**
     * @param AssetManager $manager
     * @param string $dir
     */
    function __construct(AssetManager $manager, $dir)
    {
        $this->manager = $manager;
        $this->dir = $dir;
    }

    public function writeAllAssets()
    {
        $writer = new AssetWriter($this->dir);
        $writer->writeManagerAssets($this->manager);
    }
}
