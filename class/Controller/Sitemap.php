<?php
/**
 * Рисует карту сайта по средствам модели страницы
 */
class Controller_Sitemap extends Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setTitle(t('Sitemap'));
        $this->request->setTemplate('inner');

        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', t('Home'));
        $bc->addPiece(null, $this->request->getTitle());

        return array('parent'=>0,'level'=>2);
    }

    public function xmlAction()
    {
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->appendChild( $urlset = $dom->createElement('urlset') );
        $urlset->setAttributeNS('','xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');

        $modelPage = $this->getModel('Page');
        $pages = $modelPage->findAll('hidden = 0 AND deleted = 0 AND protected = 0');

        $host = $this->request->getSchemeAndHttpHost();

        array_map(function (\Module\Page\Object\Page $page) use ($urlset, $host) {
            $urlset->appendChild($url = $urlset->ownerDocument->createElement('url'));
            $url->appendChild($url->ownerDocument->createElement('loc', $host . '/' . $page->alias));
            $url->appendChild($url->ownerDocument->createElement('lastmod', strftime('%Y-%m-%d', $page->update)));
        }, iterator_to_array($pages));

        $this->setAjax(true);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }
}
