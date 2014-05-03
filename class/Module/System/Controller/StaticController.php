<?php
/**
 * Static manager
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Controller;

use Assetic\Asset\AssetCollection;
use Sfcms\Controller;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends Controller
{
    /**
     * Resolve static/site.js file
     */
    public function assetAction()
    {
        $this->request->setAjax(true, 'js');

        $file = str_replace('.', '_', strtolower(basename($this->request->getRequestUri())));
        $assetCollection = $this->get('asset.service')->getAsseticCollection($file);
//        $re = '/.*?([^\/]+)\.([^\/]+)$/ie';
//        $method = preg_replace($re, '"\1".ucfirst("\2")', $file);
        /** @var Response $response */
        $response = $this->wrappingJsCollection($assetCollection);

        if (false == $this->container->getParameter('assetic.debug')) {
            $cacheFile = $this->container->getParameter('assetic.output') . '/' . basename($this->request->getRequestUri());
            $configCache = new ConfigCache($cacheFile, $this->container->getParameter('assetic.debug'));

            if (!$configCache->isFresh()) {
                $configCache->write($response->getContent());
            }
        }

        return $response;
    }

    protected function wrappingJsCollection(AssetCollection $assetCollection)
    {
        $dump = $assetCollection->dump();
        $etag = md5($dump);
        $expr = 60 * 60 * 24 * 7;
        $statusCode = 200;

        $headers = array(
            'content-type'=>'application/javascript',
            'etag' => $etag,
            'last-modified' => gmdate("D, d M Y H:i:s", $assetCollection->getLastModified()) . " GMT",
        );
        if ($ifModifiedSince = $this->request->headers->get('if-modified-since')) {
            $ifModifiedSince = preg_replace("/;.*$/", "", $ifModifiedSince);
            $this->app->getLogger()->info(sprintf('last-modified: %s', $headers['last-modified']));
            $this->app->getLogger()->info(sprintf('if-modified-since: %s', $ifModifiedSince));
            if ($ifModifiedSince == $headers['last-modified']) {
                $headers['cache-control'] = sprintf('max-age: %s, must-revalidate', $expr);
                $statusCode = 304;
            }
        }
        return new Response($dump, $statusCode, $headers);
    }

    protected function siteJs()
    {
        /** @var AssetCollection $assetCollection */
        $assetCollection = $this->get('asset.service')->getAsseticCollection('site_js');

        return $this->wrappingJsCollection($assetCollection);
    }

    protected function adminJs()
    {
        /** @var AssetCollection $assetCollection */
        $assetCollection = $this->get('asset.service')->getAsseticCollection('admin_js');

        return $this->wrappingJsCollection($assetCollection);
    }
}
