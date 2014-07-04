<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Assetic\Asset\FileAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Sfcms\View\Layout;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Request;

class Admin extends Layout
{
    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        /** @var AssetFactory $af */
        $af = $this->app->getContainer()->get('asset.factory');
        /** @var AssetManager $am */
        $am = $this->app->getContainer()->get('asset.manager');

        $request = $event->getRequest();

        $this->getTpl()->assign('response', $event->getResponse());

        $content = $this->getTpl()->fetch($request->getTemplate());
        $content = str_replace('</body>', $this->getScripts($event->getRequest()) . PHP_EOL . '</body>', $content);

        $event->getResponse()->setContent($content);

        return $event;
    }

    /**
     * Вернет список скриптов, для вставки в конец body
     * @param Request $request
     * @return string
     */
    private function getScripts(Request $request)
    {
        $return = [];

        $rjsConfig = [
            'baseUrl'=> '/static',
            'config' => [
                'locale' => $request->getLocale(),
            ],
            'shim' => [
                'jquery-ui' => ['deps' => ['jquery'], 'exports' => 'jquery'],
                'bootstrap' => ['deps' => ['jquery'], 'exports' => 'jquery'],
                'backbone' => ['deps' => ['underscore'], 'exports' => 'Backbone'],
                'underscore' => ['exports' => '_'],
            ],
            'paths'=> [
                'jquery' => 'system/vendor/jquery-1.11.1',
                'jquery-ui' => 'system/vendor/jquery-ui',
                'bootstrap' => 'system/vendor/twbs/js/bootstrap',
                'backbone' => 'system/vendor/backbone',
                'underscore' => 'system/vendor/underscore',
                'fancybox' => 'system/jquery/fancybox/jquery.fancybox-1.3.1.pack',
                'theme' => '/themes/'.$this->config['theme'],
                'i18n'  => 'i18n/'.$request->getLocale(),
            ],
            'map' => [
                '*' => [],
            ],
        ];

        if ('en' != $request->getLocale()) {
            $rjsConfig['paths']['datepicker_i18n'] = 'system/vendor/jquery-ui/i18n/datepicker-'.$request->getLocale();
        }

        if (file_exists(ROOT . '/' . $this->path['css'] . '/wysiwyg.css')) {
            $rjsConfig['config']['system/editor/ckeditor'] = array(
                'style' => $this->path['css'] . '/wysiwyg.css',
            );
        }

        $rjsConfig['paths']['app'] = 'admin';
        $rjsConfig['shim']['ckeditor/adapters/jquery'] = ['ckeditor/ckeditor'];

//        $rjsConfig['paths']['elfinder'] = 'admin/jquery/elfinder/elfinder';
        $rjsConfig['paths']['ckeditor'] = 'ckeditor';

        $rjsConfig['map']['*'] += array(
            'wysiwyg' => 'system/editor/'.($this->app->getContainer()->getParameter('editor')), // tinymce, ckeditor, elrte
        );

        $controllerJs = $request->getAdminScript();
        $rjsConfig['config']['system/admin']['use_controller'] = true;
        $rjsConfig['map']['*']['controller'] = $controllerJs;

        $rjsConfig['map']['*']['jqgrid'] = 'admin/jquery/jqgrid/jqgrid';

        $json = defined('JSON_PRETTY_PRINT') && $this->app->isDebug()
            ? json_encode($rjsConfig, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK)
            : json_encode($rjsConfig, JSON_NUMERIC_CHECK);

        $return[] = '<script type="text/javascript">var require = '.$json.';</script>';
        $return[] = "<script type='text/javascript' src='/static/system/vendor/require.js' data-main='system/app'></script>";

        return join(PHP_EOL, $return);
    }
}
