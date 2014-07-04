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

    /** @var string */
    private $dir;

    public function __construct(AssetFactory $factory, ContainerInterface $container, $dir = null)
    {
        $this->factory = $factory;
        $this->dir = $dir;
        $this->setContainer($container);
    }

    public function addAsseticName($name)
    {
        $this->asseticNames[] = $name;
    }

    /**
     * @return AssetFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
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
                $config['filters'] = isset($config['filters']) ? $config['filters'] : [];
                $config['options'] = isset($config['options']) ? $config['options'] : [];
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
