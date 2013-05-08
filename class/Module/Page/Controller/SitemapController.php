<?php
/**
 * Рисует карту сайта по средствам модели страницы
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Controller;

use DOMDocument;
use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends \Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setTitle(t('Sitemap'));
        $this->request->setTemplate('inner');

        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', t('Home'));
        $bc->addPiece(null, $this->request->getTitle());

        /** @var $modelPage PageModel */
        $modelPage = $this->getModel('Page');

        return $this->render('sitemap.index', array(
                'data' => $modelPage->createParentsIndex(), 'parent' => 1, 'level' => 5
            ));
    }

    public function xmlAction()
    {
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->appendChild( $urlset = $dom->createElement('urlset') );
        $urlset->setAttributeNS('','xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');

        /** @var $modelPage PageModel */
        $modelPage = $this->getModel('Page');
        $pages = $modelPage->getAll();

        $host = $this->request->getSchemeAndHttpHost();

        array_map(function (Page $page) use ($urlset, $host) {
                if ($page->link) {
                    return false;
                }
                $urlset->appendChild($url = $urlset->ownerDocument->createElement('url'));
                $url->appendChild($url->ownerDocument->createElement('loc', $host . '/' . $page->alias));
                $url->appendChild($url->ownerDocument->createElement('lastmod', strftime('%Y-%m-%d', $page->update)));
            }, iterator_to_array($pages));

        $this->setAjax(true);
        $dom->formatOutput = true;
        $response = new Response($dom->saveXML());
        $response->headers->set('Content-type', $this->request->getMimeType('xml'), true);
        return $response;
    }
}