<?php
/**
 * Рисует карту сайта по средствам модели страницы
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Controller;

use DOMDocument;
use Module\Page\Component\SiteMap\SiteMapItem;
use Module\Page\Event\SiteMapEvent;
use Sfcms\Controller;
use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function indexAction()
    {
        $this->request->setTitle($this->t('Sitemap'));
        $this->request->setTemplate('inner');

        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', $this->t('Home'));
        $bc->addPiece(null, $this->request->getTitle());

        /** @var $modelPage PageModel */
        $modelPage = $this->getModel('Page');

        return $this->render('sitemap.index', array(
                'data' => $modelPage->getParents(), 'parent' => 1, 'level' => 5
            ));
    }

    public function xmlAction()
    {
        $this->request->setAjax(true);

        $dom = new DOMDocument('1.0','UTF-8');
        $dom->appendChild( $urlset = $dom->createElement('urlset') );
        $urlset->setAttributeNS('','xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');

        $event = new SiteMapEvent($this->request);
        $this->getEventDispatcher()->dispatch(SiteMapEvent::EVENT_CONSTRUCT, $event);

        array_map(
            function (SiteMapItem $item) use ($urlset) {
                $urlset->appendChild($url = $urlset->ownerDocument->createElement('url'));
                $url->appendChild($url->ownerDocument->createElement('loc', $item->getLoc()));
                $url->appendChild($url->ownerDocument->createElement('lastmod', $item->getLastmodLong()));
                $url->appendChild($url->ownerDocument->createElement('changefreq', $item->getChangefreq()));
                $url->appendChild($url->ownerDocument->createElement('priority', $item->getPriority()));
            },
            iterator_to_array($event->getMap())
        );

        $dom->formatOutput = true;
        $response = new Response($dom->saveXML());
        $response->headers->set('Content-type', $this->request->getMimeType('xml'), true);
        return $response;
    }
}
