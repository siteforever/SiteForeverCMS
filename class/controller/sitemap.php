<?php
/**
 * Рисует карту сайта по средствам модели страницы
 */
class Controller_Sitemap extends Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setTitle('Карта сайта');
        $this->request->setTemplate('inner');

        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', 'Главная');
        $bc->addPiece(null, $this->request->getTitle());

        $tree   = $this->getModel('Page')->getMenu(1, 5);

        return '<div class="sitemap">' . $tree . '</div>';
    }

    public function xmlAction()
    {
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->appendChild( $urlset = $dom->createElement('urlset') );
        $urlset->setAttributeNS('','xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');

        $modelPage = $this->getModel('Page');
        $pages = $modelPage->findAll('hidden = 0 AND deleted = 0 AND protected = 0');

        $domain = $this->app()->getConfig('siteurl');

        array_map( function( Data_Object_Page $page ) use ( $urlset, $domain ) {
            $urlset->appendChild( $url = $urlset->ownerDocument->createElement('url') );
            $url->appendChild( $url->ownerDocument->createElement(
                'loc', $domain . '/' . ('index' == $page->alias ? '' : $page->alias)
            ) );
            $url->appendChild( $url->ownerDocument->createElement('lastmod', strftime( '%Y-%m-%d', $page->update ) ) );
        }, iterator_to_array( $pages ) );

        $this->setAjax(true);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }
}
