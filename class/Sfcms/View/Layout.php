<?php
/**
 * Представление с layout
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Sfcms\Kernel\KernelEvent;

class Layout extends ViewAbstract
{
//    const JQ_UI_THEME = 'redmond';
    const JQ_UI_THEME = 'flick';
    const JQ_UI_VERSION = '1.9.2';

    private $use_less = false; // Need using less library
    private $anti_cache = 0; // Anti cache hash

    public $path;

    protected final function init()
    {
        $this->anti_cache = substr( md5(mktime(null,0,0)), 0, 8 );

        /** @var $theme string */
        $theme = $this->_app->getConfig('template.theme');

        $this->path = array(
            'css'    => '/themes/' . $theme . '/css',
            'js'     => '/themes/' . $theme . '/js',
            'images' => '/themes/' . $theme . '/images',
            'misc'   => '/misc',
        );

        /** Данные шаблона */
        $this->getTpl()->assign( array(
            'path'     => $this->path,
            'resource' => $this->getRequest()->get('resource'),
            'template' => $this->getRequest()->getTemplate(),
            'config'   => $this->_app->getConfig(),
            'feedback' => $this->getRequest()->getFeedbackString(),
            'host'     => $this->getRequest()->getHost(),
            'request'  => $this->getRequest(),
        ) );
    }


    /**
     * @param KernelEvent $event
     * @return string
     */
    public function view(KernelEvent $event)
    {
        $event->getResponse()->setCharset('utf-8');
        $event->getResponse()->headers->set('Content-type', 'text/html');
        $this->init();

        $this->selectLayout()->view($event);

        $head = '<head>' . PHP_EOL . $this->getHead();
        $scripts = $this->getScripts() . PHP_EOL . '</body>';

        $content = $event->getResponse()->getContent();
        $content = str_replace(
            '<html>', sprintf('<html lang="%s">', $this->_app->getConfig('language')), $content
        );
        $content = str_replace('<head>', $head, $content);
        $content = str_replace('</body>', $scripts, $content);

        $content = preg_replace( '/[ \t]+/', ' ', $content );
        $content = preg_replace( '/\n[ \t]+/', "\n", $content );
        $content = preg_replace( '/\n+/', "\n", $content );

        $event->getResponse()->setContent($content);

        return $event;
    }

    /**
     * Вернет список тэгов для head
     * @return string
     */
    private function getHead()
    {
        $request = $this->getRequest();
        $config = $this->_app->getConfig();

        $return = array();
        $return[] = "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />";
        $return[] = "<meta name=\"generator\" content=\"SiteForever CMS\" />";
        $return[] = "<title>".strip_tags( $request->getTitle() ).' / '.$config->get('sitename')."</title>";

        if ( $request->getKeywords() ) {
            $return[] = "<meta name=\"keywords\" content=\"".$request->getKeywords()."\" />";
        }
        if ( $request->getDescription() ) {
            $return[] = "<meta name=\"description\" content=\"".$request->getDescription()."\" />";
        }

        $return[] = "<link title=\"\" type=\"application/rss+xml\" rel=\"alternate\" href=\"http://{$_SERVER['HTTP_HOST']}/rss\" />";

        if (file_exists(ROOT . DS . 'favicon.png')) {
            $return[] = "<link rel=\"icon\" type=\"image/png\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.png\" />";
        } elseif (file_exists(ROOT . DS . 'favicon.ico')) {
            $return[] = "<link rel=\"icon\" type=\"image/ico\" href=\"http://{$_SERVER['HTTP_HOST']}/favicon.ico\" />";
        }

        if ($request->get('admin')) {
            $this->_app->addStyle('/static/admin/jquery/jqgrid/ui.jqgrid.css');
        }

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
        }, $this->_app->getStyle()) );

        return join(PHP_EOL, $return);
    }

    /**
     * Вернет список скриптов, для вставки в конец body
     * @return string
     */
    private function getScripts()
    {
        $return = array();
        $request = $this->getRequest();
        $config = $this->_app->getConfig();

        $rjsConfig = array(
            'baseUrl'=> '/misc',
            'config' => array(
                '*' => array(
                    'lang' => $this->_app->getConfig('language'),
                ),
            ),
            'shim' => array(
                'jui'   => array('jquery'),
                'etc/catalog' => array('jquery','jquery/jquery.gallery'),
                'jquery/jquery.gallery' => array('jquery','fancybox'),
            ),
            'paths'=> array(
                'fancybox' => 'jquery/fancybox/jquery.fancybox-1.3.1' . (\App::isDebug() ? '' : '.pack'),
                'jui' => 'jquery/jquery-ui-'.Layout::JQ_UI_VERSION.'.custom.min',
                'twitter' => 'bootstrap/js/bootstrap' . (\App::isDebug() ? '' : '.min'),
                'siteforever' => 'module/siteforever',
                'runtime' => '../runtime',
                'theme' => '/themes/'.$this->_app->getConfig('template.theme'),
                'i18n'  => '../static/i18n/'.$this->_app->getConfig('language'),
            ),
            'map' => array(
                '*' => array(
                ),
            ),
        );

        if ( $request->get('admin') ) {

            $rjsConfig['paths']['app'] = 'admin';
//            $rjsConfig['paths']['controller'] = ;
            $rjsConfig['shim']['elfinder/js/i18n/elfinder.ru'] = array('elfinder/js/elfinder');
            $rjsConfig['shim']['ckeditor/adapters/jquery'] = array('ckeditor/ckeditor');

            $rjsConfig['map']['*'] += array(
                'wysiwyg' => 'admin/editor/'.($config->get('editor')?:'ckeditor'), // tinymce, ckeditor, elrte
                'elfinder/js/elfinder' => 'elfinder/js/elfinder' . (\App::isDebug() ? '.full' : '.min'),
//                'jqgrid'  => 'admin/jquery/jqgrid',
                'controller' => 'admin/'.$request->getController(),
            );

            $rjsConfig['map']['*']['jqgrid'] = '../static/admin/jquery/jqgrid/jqgrid';

            $return[] = '<script type="text/javascript">var require = '.json_encode($rjsConfig).';</script>';


            if ( file_exists(SF_PATH.'/_runtime/asset/require-jquery-min.js') ) {
                $return[] = "<script type='text/javascript' "
                    . "src='/_runtime/asset/require-jquery-min.js' data-main='../_runtime/asset/admin-min'>"
                    . "</script>";
            } else {
                $return[] = "<script type='text/javascript' "
                    . "src='/misc/require-jquery.js' data-main='admin/app'>"
                    . "</script>";
            }

        } else {
            $return[] = '<script type="text/javascript">var require = '.json_encode($rjsConfig).';</script>';
            $return[] = "<script type='text/javascript' src='/misc/require-jquery-min.js' data-main='site'></script>";
        }


        if ( $this->use_less ) {
            $return[] = '<script type="text/javascript" src="/misc/less-1.3.0.min.js"></script>';
        }

        return join(PHP_EOL, $return);
    }

    /**
     * Выбор лэйаута
     * @return Layout
     */
    protected function selectLayout()
    {
        if ('system:' == $this->getRequest()->get('resource')) {
            $layout = new Layout\Admin($this->_app);
            $this->getRequest()->set('admin', true);
        } else {
            $layout = new Layout\Page($this->_app);
            $this->getRequest()->set('admin', false);
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


    /**
     * Attach jQueryUI plugin
     */
    protected function attachJUI()
    {
        $this->_app->addStyle( $this->getMisc().'/jquery/'.self::JQ_UI_THEME.'/jquery-ui-'.self::JQ_UI_VERSION.'.custom.css' );
        $this->_app->addScript( $this->getMisc().'/jquery/jquery-ui-'.self::JQ_UI_VERSION.'.custom.min.js' );
    }
}
