<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Assetic\Asset\FileAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Sfcms\View\Layout;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Request;

class Admin extends Layout
{
    const JQ_UI_THEME = 'flick';

    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        /** @var AssetManager $am */
        $am = $this->app->getContainer()->get('asset.manager');
        /** @var AssetWriter $writer */
        $writer = $this->app->getContainer()->get('asset.writer');

        $this->app->addStyle($this->getMisc().'/jquery/'.self::JQ_UI_THEME.'/jquery-ui.min.css');
//        $this->_app->addScript( $this->getMisc().'/jquery/jquery-ui-'.self::JQ_UI_VERSION.'.custom.min.js' );

        $request = $event->getRequest();

        $this->app->getAssets()->addStyle('/static/admin/jquery/elfinder/elfinder.css');
        $this->app->getAssets()->addScript('/static/admin.js');
        $this->app->getAssets()->addStyle($this->getMisc() . '/bootstrap/css/bootstrap.css');
        $this->app->getAssets()->addStyle('/static/admin/jquery/jqgrid/ui.jqgrid.css');
        $am->set('admIcons', new FileAsset(realpath(__DIR__ . '/../../../Module/System/Static/icons.css')));
        $am->get('admIcons')->setTargetPath('admIcons.css');
        $this->app->getAssets()->addStyle('/static/admIcons.css');
        $this->app->getAssets()->addStyle('/misc/bootstrap/css/bootstrap-datetimepicker.min.css');
        $this->app->getAssets()->addStyle($this->getMisc() . '/admin/admin.css');

        $this->getTpl()->assign('response', $event->getResponse());

        $content = $this->getTpl()->fetch($request->getTemplate());
        $content = str_replace('</head>', $this->getStyles($event->getRequest()) . PHP_EOL . '</head>', $content);
        $content = str_replace('</body>', $this->getScripts($event->getRequest()) . PHP_EOL . '</body>', $content);

        $event->getResponse()->setContent($content);

        return $event;
    }

    public function getStyles(Request $request)
    {
        $return = array();

        // Подключение стилей в заголовок
        $return = array_merge( $return, array_map(function($style) {
                    return sprintf('<link type="text/css" rel="stylesheet" href="%s">', $style);
                }, $this->app->getAssets()->getStyle()) );

        return join(PHP_EOL, $return);
    }

    /**
     * Вернет список скриптов, для вставки в конец body
     * @param Request $request
     * @return string
     */
    private function getScripts(Request $request)
    {
        $return = array();

        $rjsConfig = array(
            'baseUrl'=> '/misc',
            'config' => array(
            ),
            'shim' => array(
                'jui'   => array('jquery'),
                'etc/catalog' => array('jquery','jquery/jquery.gallery'),
                'jquery/jquery.gallery' => array('jquery','fancybox'),
            ),
            'paths'=> array(
                'fancybox' => 'jquery/fancybox/jquery.fancybox-1.3.1' . (\App::isDebug() ? '' : '.pack'),
                'theme' => '/themes/'.$this->config['theme'],
                'i18n'  => '../static/i18n/'.$this->app->getContainer()->getParameter('language'),
            ),
            'map' => array(
                '*' => array(
                ),
            ),
        );

        if ($request->isSystem() || $this->app->getContainer()->getParameter('assetic.bootstrap')) {
            $rjsConfig['paths']['twitter'] = 'bootstrap/js/bootstrap' . ($this->app->isDebug() ? '' : '.min');
        }

        if ($request->isSystem()) {
            if (file_exists(ROOT . '/' . $this->path['css'] . '/wysiwyg.css')) {
                $rjsConfig['config']['admin/editor/ckeditor'] = array(
                    'style' => $this->path['css'] . '/wysiwyg.css',
                );
            }

            $rjsConfig['paths']['app'] = 'admin';
            $rjsConfig['paths']['jui'] = 'jquery/jquery-ui.min';
            $rjsConfig['paths']['twitter'] = 'bootstrap/js/bootstrap' . ($this->app->isDebug() ? '' : '.min');
            if ('en' != $request->getLocale()) {
                $rjsConfig['shim']['bootstrap/js/locales/bootstrap-datetimepicker.'.$request->getLocale()] = array('bootstrap/js/bootstrap-datetimepicker');
            }
            $rjsConfig['shim']['ckeditor/adapters/jquery'] = array('ckeditor/ckeditor');

            $rjsConfig['paths']['elfinder'] = '../static/admin/jquery/elfinder/elfinder';
            $rjsConfig['paths']['ckeditor'] = '../static/ckeditor';

            $rjsConfig['map']['*'] += array(
                'wysiwyg' => 'admin/editor/'.($this->app->getContainer()->getParameter('editor')), // tinymce, ckeditor, elrte
            );

            $controllerJs = $request->getAdminScript();
            if ('admin' == substr($controllerJs, 0, 5)) {
                $controllerFile = ROOT . $this->getMisc() . '/' . $controllerJs . '.js';
            } else {
                $controllerFile = ROOT . '/' . $controllerJs . '.js';
            }

            if (file_exists($controllerFile)) {
                $rjsConfig['config']['admin/admin']['use_controller'] = true;
                if ('static' == substr($controllerJs, 0, 6)) {
                    $controllerJs = '../' . $controllerJs;
                }
                $rjsConfig['map']['*']['controller'] = $controllerJs;
            }

            $rjsConfig['map']['*']['jqgrid'] = '../static/admin/jquery/jqgrid/jqgrid';

            $json = defined('JSON_PRETTY_PRINT') && $this->app->isDebug()
                ? json_encode($rjsConfig, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK)
                : json_encode($rjsConfig, JSON_NUMERIC_CHECK);

            $return[] = '<script type="text/javascript">var require = '.$json.';</script>';


            $return[] = "<script type='text/javascript' src='/static/require-vendors.js' data-main='../static/admin'></script>";
        } else {
            $rjsConfig['paths']['site'] = '../static/site';


            $return[] = '<script type="text/javascript">var require = '.json_encode($rjsConfig).';</script>';
            $return[] = "<script type='text/javascript' src='/static/require-vendors.js' data-main='site'></script>";
        }

        return join(PHP_EOL, $return);
    }
}
