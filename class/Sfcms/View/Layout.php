<?php
/**
 * Представление с layout
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Request;

class Layout extends ViewAbstract
{
    private $antiCache = 0; // Anti cache hash

    public $path;

    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        $event->getResponse()->setCharset('utf-8');
        $event->getResponse()->headers->set('Content-type', 'text/html');

        $this->antiCache = substr(md5(uniqid()), 0, 8);

        /** Данные шаблона */
        $this->getTpl()->assign( array(
                'path'     => $this->path,
                'resource' => $event->getRequest()->get('resource'),
                'template' => $event->getRequest()->getTemplate(),
                'feedback' => $event->getRequest()->getFeedbackString(),
                'host'     => $event->getRequest()->getHost(),
                'request'  => $event->getRequest(),
            ));

        $this->selectLayout($event->getRequest())->view($event);

        $this->app->getContainer()->get('asset.writer');

        $content = $event->getResponse()->getContent();
        $content = str_replace('<head>', '<head>' . PHP_EOL . $this->getHead($event->getRequest()), $content);

        if (!$this->app->isDebug()) {
            $content = preg_replace( '/[ \t]+/', ' ', $content );
            $content = preg_replace( '/\n[ \t]+/', "\n", $content );
            $content = preg_replace( '/\n+/', "\n", $content );
        }
        $event->getResponse()->setContent($content);

        return $event;
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
            ? new Layout\Admin($this->app, $this->config)
            : new Layout\Page($this->app, $this->config);
        if ($request->isSystem()) {
            $request->set('modules', $this->app->adminMenuModules());
        }
        return $layout;
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

        $return[] = "<title>" . strip_tags($request->getTitle()) . ' / '
            . $this->app->getContainer()->getParameter('sitename') . "</title>";

        if ( $request->getKeywords() ) {
            $return[] = "<meta name=\"keywords\" content=\"".$request->getKeywords()."\">";
        }
        if ( $request->getDescription() ) {
            $return[] = "<meta name=\"description\" content=\"".$request->getDescription()."\">";
        }

        if (!$this->app->getContainer()->hasParameter('silent')) {
            $return[] = '<meta name="generator" content="SiteForever CMS">';
        }

        return join(PHP_EOL, $return);
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
