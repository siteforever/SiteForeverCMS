<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Service;


use Assetic\Asset\AssetCollection;
use Assetic\Factory\AssetFactory;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AsseticService extends ContainerAware
{
    /** @var  AssetFactory */
    private $factory;

    private $asseticNames = array();

    public function __construct(AssetFactory $factory, ContainerInterface $container)
    {
        $this->factory = $factory;
        $this->setContainer($container);
    }

    public function addAsseticName($name)
    {
        $this->asseticNames[] = $name;
    }

    /**
     * @param $name
     * @return AssetCollection
     * @throws \RuntimeException
     */
    public function getAsseticCollection($name)
    {
        $am = $this->factory->getAssetManager();
        if (!$am->getNames()) {
            foreach ($this->asseticNames as $aName) {
                $config = $this->container->getParameter($aName);
                $assetCollection = $this->factory->createAsset($config['inputs'], $config['filters'], $config['options']);
                $am->set(preg_replace('/^.*?([^\.]+)$/', '$1', $aName), $assetCollection);
            }
        }

        if (!$am->has($name)) {
            throw new \RuntimeException(sprintf('Assetic "%s" not found', $name));
        }
        return $am->get($name);
    }
}
