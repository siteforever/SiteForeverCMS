<?php
/**
 * Static manager
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Controller;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Asset\StringAsset;
use Assetic\Factory\AssetFactory;
use Sfcms\Controller;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends Controller
{
    /**
     * Resolve static/site.js file
     */
    public function assetAction()
    {
        $response = new Response();
        $this->request->setAjax(true, 'js');

        $file = $this->request->getRequestUri();
        $re = '/.*?([^\/]+)\.([^\/]+)$/ie';
        $method = preg_replace($re, '"\1".ucfirst("\2")', $file);
        $response->setContent($this->$method()->dump());
        if (!$this->app->isDebug()) {
            file_put_contents($this->config->get('static_dir') . '/' . basename($file), $response->getContent());
        }
        return $response;
    }

    protected function siteJs()
    {
        $assetCollection = new AssetCollection(array(
            new FileAsset(ROOT . '/misc/site.js'),
            new FileAsset(ROOT . '/misc/jquery/fancybox/jquery.fancybox-1.3.1.js'),
            new StringAsset('define("fancybox");'),
            new FileAsset(ROOT . '/misc/module/siteforever.js'),
            new FileAsset(ROOT . '/static/i18n/'.$this->request->getLocale() . '.js'),
            new FileAsset(ROOT . '/misc/jquery/jquery.blockUI.js'),
            new StringAsset('define("jquery/jquery.blockUI");'),
            new FileAsset(ROOT . '/misc/jquery/jquery.form.js'),
            new StringAsset('define("jquery/jquery.form");'),
            new FileAsset(ROOT . '/misc/jquery/jquery.gallery.js'),
            new FileAsset(ROOT . '/misc/jquery/jquery.captcha.js'),
            new FileAsset(ROOT . '/misc/module/console.js'),
            new FileAsset(ROOT . '/misc/module/basket.js'),
            new FileAsset(ROOT . '/misc/module/behavior.js'),
            new FileAsset(ROOT . '/misc/module/catalog.js'),
            new FileAsset(ROOT . '/misc/module/form.js'),
            new FileAsset(ROOT . '/misc/module/alert.js'),
        ));
        if (!$this->config->get('misc.noBootstrap')) {
            $assetCollection->add(new FileAsset(ROOT . '/misc/bootstrap/js/bootstrap.js'));
            $assetCollection->add(new StringAsset('define("twitter");'));
        }
        return $assetCollection;
    }

    protected function siteCss()
    {
        $assetCollection = new AssetCollection(array(
            new FileAsset(ROOT  .'/misc/jquery/fancybox/jquery.fancybox-1.3.1.css'),
        ));
        if (!$this->config->get('misc.noBootstrap')) {
            $assetCollection->add(new FileAsset(ROOT . '/misc/bootstrap/css/bootstrap.css'));
        }
        return $assetCollection;
    }

    protected function adminJs()
    {
        $assetCollection = new AssetCollection(array(
            new FileAsset(ROOT . '/misc/admin/admin.js'),
            new FileAsset(ROOT . '/misc/jquery/fancybox/jquery.fancybox-1.3.1.js'),
            new StringAsset('define("fancybox");'),
            new GlobAsset(ROOT . '/mics/admin/jquery/*'),
            new GlobAsset(ROOT . '/mics/admin/catalog/*'),
            new FileAsset(ROOT . '/misc/module/siteforever.js'),
            new FileAsset(ROOT . '/static/i18n/'.$this->request->getLocale() . '.js'),
            new FileAsset(ROOT . '/misc/jquery/jquery.blockUI.js'),
            new StringAsset('define("jquery/jquery.blockUI");'),
            new FileAsset(ROOT . '/misc/jquery/jquery.form.js'),
            new StringAsset('define("jquery/jquery.form");'),
            new FileAsset(ROOT . '/misc/jquery/jquery.gallery.js'),
            new FileAsset(ROOT . '/misc/jquery/jquery.captcha.js'),
            new FileAsset(ROOT . '/misc/module/console.js'),
            new FileAsset(ROOT . '/misc/module/basket.js'),
            new FileAsset(ROOT . '/misc/module/behavior.js'),
            new FileAsset(ROOT . '/misc/module/catalog.js'),
            new FileAsset(ROOT . '/misc/module/form.js'),
            new FileAsset(ROOT . '/misc/module/alert.js'),

            new FileAsset(ROOT . '/misc/admin/app.js'),
        ));

        if (!$this->config->get('misc.noBootstrap')) {
            $assetCollection->add(new FileAsset(ROOT . '/misc/bootstrap/js/bootstrap.js'));
            $assetCollection->add(new StringAsset('define("twitter");'));
        }
        return $assetCollection;
    }

    protected function adminCss()
    {
        $assetCollection = new AssetCollection(array(
            new FileAsset(ROOT  .'/misc/jquery/fancybox/jquery.fancybox-1.3.1.css'),
            new FileAsset(ROOT . '/misc/bootstrap/css/bootstrap.css'),
        ));
        return $assetCollection;
    }
}
