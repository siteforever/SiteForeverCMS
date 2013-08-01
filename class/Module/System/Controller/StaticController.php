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
        $this->request->setAjax(true, 'js');

        $file = $this->request->getRequestUri();
        $re = '/.*?([^\/]+)\.([^\/]+)$/ie';
        $method = preg_replace($re, '"\1".ucfirst("\2")', $file);
        /** @var Response $response */
        $response = $this->$method();
        if (!($this->app->isDebug() || $this->filesystem->exists($this->config->get('static_dir') . basename($file)))) {
            $this->filesystem->dumpFile($this->config->get('static_dir') . basename($file), $response->getContent());
        }
        return $response;
    }

    protected function siteJs()
    {
        $assetCollection = new AssetCollection(array(
            new FileAsset(SF_PATH . '/misc/site.js'),
            new FileAsset(SF_PATH . '/misc/jquery/fancybox/jquery.fancybox-1.3.1.js'),
            new StringAsset('define("fancybox");'),
            new FileAsset(SF_PATH . '/misc/module/siteforever.js'),
            new FileAsset(ROOT . '/static/i18n/'.$this->request->getLocale() . '.js'),
            new FileAsset(SF_PATH . '/misc/jquery/jquery.blockUI.js'),
            new StringAsset('define("jquery/jquery.blockUI");'),
            new FileAsset(SF_PATH . '/misc/jquery/jquery.form.js'),
            new StringAsset('define("jquery/jquery.form");'),
            new FileAsset(SF_PATH . '/misc/jquery/jquery.gallery.js'),
            new FileAsset(SF_PATH . '/misc/jquery/jquery.captcha.js'),
            new FileAsset(SF_PATH . '/misc/module/console.js'),
            new FileAsset(SF_PATH . '/misc/module/basket.js'),
            new FileAsset(SF_PATH . '/misc/module/behavior.js'),
            new FileAsset(SF_PATH . '/misc/module/catalog.js'),
            new FileAsset(SF_PATH . '/misc/module/form.js'),
            new FileAsset(SF_PATH . '/misc/module/alert.js'),
        ));

        if (!$this->config->get('misc.noBootstrap')) {
            $assetCollection->add(new FileAsset(ROOT . '/misc/bootstrap/js/bootstrap.js'));
            $assetCollection->add(new StringAsset('define("twitter");'));
        }
        return new Response($assetCollection->dump(), 200, array('content-type'=>'text/css'));
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
        return new Response($assetCollection->dump(), 200, array('content-type'=>'application/javascript'));;
    }
}
