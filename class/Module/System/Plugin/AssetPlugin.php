<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Plugin;

use Assetic\Asset\AssetCache;
use Assetic\AssetManager;
use Assetic\Cache\FilesystemCache;
use Assetic\Factory\AssetFactory;
use Assetic\Util\TraversableString;
use Doctrine\Common\Collections\ArrayCollection;

class AssetPlugin
{
    /** @var AssetManager */
    private $assetManager;

    /** @var AssetFactory */
    private $assetFactory;

    /** @var ArrayCollection  */
    private $scopes;

    private $styleSheetTemplate = '<link rel="stylesheet" type="text/css" href="/%s">';

    private $javaScriptTemplate = '<script type="text/javascript" src="/%s"></script>';

    public function __construct(AssetFactory $assetFactory)
    {
        $this->assetFactory = $assetFactory;
        $this->assetManager = $assetFactory->getAssetManager();
        $this->scopes = new ArrayCollection();
    }

    public function addScope($key, $value)
    {
        $this->scopes->set($key, $value);
    }

    public function removeScope($key)
    {
        $this->scopes->remove($key);
    }

    public function issetScope($key)
    {
        return $this->scopes->containsKey($key);
    }

    public function getScope($key)
    {
        return $this->scopes->get($key);
    }

    /**
     * style function plugin
     *
     * @param $params
     * @param \Smarty_Internal_Template $smarty
     * @return array
     */
    public function functionStyle($params, \Smarty_Internal_Template $smarty)
    {
        list($inputs, $filters, $options) = $this->prepareAsset($params);

        $urls = $this->asseticStylesheets($inputs, $filters, $options);
        $return = [];
        if ($urls->count()) {
            foreach ($urls as $url) {
                $return[] = sprintf($this->styleSheetTemplate, trim($url, '/'));
            }
        } else {
            $return[] = sprintf($this->styleSheetTemplate, trim($urls, '/'));
        }

        return join(PHP_EOL, $return);
    }

    /**
     * Returns an array of stylesheet URLs.
     *
     * @param array|string $inputs  Input strings
     * @param array|string $filters Filter names
     * @param array        $options An array of options
     *
     * @return TraversableString|array An array of stylesheet URLs
     */
    private function asseticStylesheets($inputs = array(), $filters = array(), array $options = array())
    {
        if (!isset($options['output'])) {
            $options['output'] = 'static/css/*.css';
        }

        return $this->asseticUrls($inputs, $filters, $options);
    }

    /**
     * js function plugin
     *
     * @param $params
     * @param \Smarty_Internal_Template $smarty
     * @return string
     */
    public function functionJs($params, \Smarty_Internal_Template $smarty)
    {
        list($inputs, $filters, $options) = $this->prepareAsset($params);

        $urls = $this->asseticJavascripts($inputs, $filters, $options);
        $return = [];
        if ($urls->count()) {
            foreach ($urls as $url) {
                $return[] = sprintf($this->javaScriptTemplate, trim($url, '/'));
            }
        } else {
            $return[] = sprintf($this->javaScriptTemplate, trim($urls, '/'));
        }

        return join(PHP_EOL, $return);
    }

    /**
     * Returns an array of javascript URLs.
     *
     * @param array|string $inputs  Input strings
     * @param array|string $filters Filter names
     * @param array        $options An array of options
     *
     * @return TraversableString|array An array of javascript URLs
     */
    private function asseticJavascripts($inputs = array(), $filters = array(), array $options = array())
    {
        if (!isset($options['output'])) {
            $options['output'] = 'static/js/*.js';
        }

        return $this->asseticUrls($inputs, $filters, $options);
    }

    /**
     * @param $params
     * @return \Assetic\Asset\AssetCollection
     */
    private function prepareAsset($params)
    {
        $inputs = [];
        if (isset($params['file'])) {
            $inputs = $params['file'];
            unset($params['file']);
        }
        $filters = [];
        if (isset($params['filters'])) {
            $filters = $params['filters'];
            unset($params['filters']);
        }

        if (!is_array($inputs)) {
            $inputs = array_filter(array_map('trim', explode(',', $inputs)));
        }

        if (!is_array($filters)) {
            $filters = array_filter(array_map('trim', explode(',', $filters)));
        }

        $inputs = array_map(function($input) {
                $scope = null;
                if (preg_match('/@([a-z]+):([a-z0-9\/\.-]+)/i', $input, $match)) {
                    $scope = $match[1];
                    $input = $match[2];
                }
                if (!$scope) return $input;
                if (!$this->scopes->containsKey($scope)) {
                    throw new \InvalidArgumentException(
                        sprintf('Scope `%s` not defined. Available [%s] scopes.', $scope, join(', ', $this->scopes->getKeys()))
                    );
                }
                return sprintf('%s/%s', $this->scopes->get($scope), $input);
            }, $inputs);

        return [$inputs, $filters, $params];
    }

    /**
     * Returns an array of asset urls.
     *
     * @param array|string $inputs  Input strings
     * @param array|string $filters Filter names
     * @param array        $options An array of options
     *
     * @return array An array of URLs
     */
    private function asseticUrls($inputs = array(), $filters = array(), array $options = array())
    {
        $coll = new AssetCache(
            $this->assetFactory->createAsset($inputs, $filters, $options),
            new FilesystemCache(ROOT . '/runtime/cache/assetic')
        );

        $debug = isset($options['debug']) ? $options['debug'] : $this->assetFactory->isDebug();
        $combine = isset($options['combine']) ? $options['combine'] : !$debug;
        $one = $coll->getTargetPath();

        $this->assetFactory->getAssetManager()->set(preg_replace('/[^a-z]+/', '_', $one), $coll);

        if (!$combine) {
            $many = array($one);
        } else {
            $many = array();
            foreach ($coll as $leaf) {
                $many[] = $leaf->getTargetPath();
            }
        }

        return new TraversableString($one, $many);
    }
}
