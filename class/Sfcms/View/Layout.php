<?php
/**
 * Представление с layout
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Request;

class Layout extends ViewAbstract
{
    private $use_less = false; // Need using less library
    private $anti_cache = 0; // Anti cache hash

    public $path;

    protected final function init(Request $request)
    {
        $this->anti_cache = substr( md5(mktime(null,0,0)), 0, 8 );

        /** Данные шаблона */
        $this->getTpl()->assign( array(
            'path'     => $this->path,
            'resource' => $request->get('resource'),
            'template' => $request->getTemplate(),
            'feedback' => $request->getFeedbackString(),
            'host'     => $request->getHost(),
            'request'  => $request,
        ));
    }


    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        $event->getResponse()->setCharset('utf-8');
        $event->getResponse()->headers->set('Content-type', 'text/html');
        $this->init($event->getRequest());

        $this->selectLayout($event->getRequest())->view($event);

        $content = $event->getResponse()->getContent();

        $content = str_replace('<head>', '<head>' . PHP_EOL . $this->getHead($event->getRequest()), $content);
        $content = str_replace('</head>', $this->getStyles($event->getRequest()) . PHP_EOL . '</head>', $content);
        $content = str_replace('</body>', $this->getScripts($event->getRequest()) . PHP_EOL . '</body>', $content);

        if (!$this->_app->isDebug()) {
            $content = preg_replace( '/[ \t]+/', ' ', $content );
            $content = preg_replace( '/\n[ \t]+/', "\n", $content );
            $content = preg_replace( '/\n+/', "\n", $content );
        }
        $event->getResponse()->setContent($content);

        return $event;
    }

    /**
     * Вернет список тэгов для head
     * @param Request $request
     * @return string
     */
    private function getHead(Request $request)
    {
        $return = array();
        $return[] = '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';
        $return[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
        $return[] = '<meta name="viewport" content="width=device-width,initial-scale=1">';
        $return[] = '<meta name="generator" content="SiteForever CMS">';
        $return[] = "<title>" . strip_tags($request->getTitle()) . ' / '
            . $this->_app->getContainer()->getParameter('sitename') . "</title>";

        if ( $request->getKeywords() ) {
            $return[] = "<meta name=\"keywords\" content=\"".$request->getKeywords()."\">";
        }
        if ( $request->getDescription() ) {
            $return[] = "<meta name=\"description\" content=\"".$request->getDescription()."\">";
        }

        $return[] = "<link title=\"\" type=\"application/rss+xml\" rel=\"alternate\" href=\"http://{$_SERVER['HTTP_HOST']}/rss\">";

        if (file_exists(ROOT . DS . 'favicon.png')) {
            $return[] = "<link rel=\"shortcut icon\" href=\"/favicon.png\" type=\"image/png\">";
        } elseif (file_exists(ROOT . DS . 'favicon.ico')) {
            $return[] = "<link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\">";
        }

        return join(PHP_EOL, $return);
    }

    public function getStyles(Request $request)
    {
        /** @var AssetManager $am */
        $am = $this->_app->getContainer()->get('asset.manager');
        /** @var AssetWriter $writer */
        $writer = $this->_app->getContainer()->get('asset.writer');

        $return = array();

        $this->_app->getAssets()->addStyle('/misc/jquery/fancybox/jquery.fancybox-1.3.1.css');

        // Подключение стилей в заголовок
        $useLess = &$this->use_less;
        $antiCache = &$this->anti_cache;
        $return = array_merge( $return, array_map(function($style) use ( &$useLess, $antiCache ) {
            if (preg_match('/.*\.css$/', $style)) {
                return "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$style}?{$antiCache}\">";
            } elseif (preg_match('/.*\.less$/', $style)) {
                $useLess = true;
                return "<link type=\"text/css\" rel=\"stylesheet/less\" href=\"{$style}?{$antiCache}\">";
            }
            return '';
        }, $this->_app->getAssets()->getStyle()) );

        $writer->writeManagerAssets($am);

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
                'i18n'  => '../static/i18n/'.$this->_app->getContainer()->getParameter('language'),
            ),
            'map' => array(
                '*' => array(
                ),
            ),
        );

        if ($request->isSystem() || $this->_app->getContainer()->getParameter('assetic.bootstrap')) {
            $rjsConfig['paths']['twitter'] = 'bootstrap/js/bootstrap' . ($this->_app->isDebug() ? '' : '.min');
        }

        if ($request->isSystem()) {
            if (file_exists(ROOT . '/' . $this->path['css'] . '/wysiwyg.css')) {
                $rjsConfig['config']['admin/editor/ckeditor'] = array(
                    'style' => $this->path['css'] . '/wysiwyg.css',
                );
            }

            $rjsConfig['paths']['app'] = 'admin';
            $rjsConfig['paths']['jui'] = 'jquery/jquery-ui.min';
            $rjsConfig['paths']['twitter'] = 'bootstrap/js/bootstrap' . ($this->_app->isDebug() ? '' : '.min');
            if ('en' != $request->getLocale()) {
                $rjsConfig['shim']['bootstrap/js/locales/bootstrap-datetimepicker.'.$request->getLocale()] = array('bootstrap/js/bootstrap-datetimepicker');
            }
            $rjsConfig['shim']['ckeditor/adapters/jquery'] = array('ckeditor/ckeditor');

            $rjsConfig['paths']['elfinder'] = '../static/admin/jquery/elfinder/elfinder';
            $rjsConfig['paths']['ckeditor'] = '../static/ckeditor';

            $rjsConfig['map']['*'] += array(
                'wysiwyg' => 'admin/editor/'.($this->_app->getContainer()->getParameter('editor')), // tinymce, ckeditor, elrte
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

            $json = defined('JSON_PRETTY_PRINT') && $this->_app->isDebug()
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

    /**
     * Выбор лэйаута
     * @param Request $request
     * @return Layout
     */
    protected function selectLayout(Request $request)
    {
        $request->attributes->set('admin', $request->isSystem());
        $layout = $request->isSystem()
            ? new Layout\Admin($this->_app, $this->config)
            : new Layout\Page($this->_app, $this->config);
        if ($request->isSystem()) {
            $request->set('modules', $this->_app->adminMenuModules());
        }
        return $layout;
    }


    /**
     * @return string
     */
    protected function getCss()
    {
        return $this->path['css'];
    }


    /**
     * @return string
     */
    protected function getJs()
    {
        return $this->path['js'];
    }


    /**
     * @return string
     */
    protected function getMisc()
    {
        return $this->path['misc'];
    }
}
