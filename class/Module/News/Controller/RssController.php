<?php
/**
 * Контроллер RSS лент
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Controller;

use Sfcms_Controller;
use Sfcms\Request;
use Module\News\Model\NewsModel;
use Module\News\Object\News;
use Sfcms\Db\Criteria;
use DOMDocument;

class RssController extends Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setAjax(true, Request::TYPE_XML);
        $this->request->setTemplate('inner');

        // @TODO Нет абсолютного подтверждения работы этого модуля
        /**
         * @var NewsModel $model
         */
        $model = $this->getModel('News');

        $criteria   = new Criteria(array(
            'cond'      => ' hidden = 0 AND protected = 0 AND deleted = 0 ',
            'params'    => array(),
            'order'     => 'date DESC',
            'limit'     => 20,
        ));

        //$crit   = ;


        $news   = $model->findAll( $criteria );

        $this->tpl->assign('data', $news);
        $this->tpl->assign('gmdate', gmdate('D, d M Y H:i:s', time()).' GTM');

        $this->request->set('getcontent', true);

        //header('Content-type: text/xml; charset=utf-8');

        $dom = new DOMDocument('1.0', 'utf-8');
        $rssDom = $dom->createElement('rss');
        $dom->appendChild( $rssDom );

        $rssDom->setAttribute('version', '2.0');
        $rssDom->appendChild( $channelDom = $dom->createElement('channel') );

        $channelDom->appendChild( $dom->createElement('title', $this->config->get('sitename')) );
        $channelDom->appendChild( $dom->createElement('link', $this->config->get('siteurl')) );
        $channelDom->appendChild( $dom->createElement('description', $this->config->get('sitename')) );
        $channelDom->appendChild( $dom->createElement('generator', 'SiteForeverCMS') );

        /** @var $article News */
        foreach( $news as $article ) {
            $description = $article['notice'];
            $description = str_replace('&nbsp;', ' ', $description);

            $channelDom->appendChild( $itemDom = $dom->createElement('item') );
            $itemDom->appendChild( $dom->createElement('title', htmlentities( $article->title ) ) );
            $itemDom->appendChild( $dom->createElement('link', $this->config->get('siteurl').'/'.$article->url) );
            $itemDom->appendChild( $dom->createElement('description', $description ) );
            $itemDom->appendChild( $dom->createElement('pubDate', date('r', $article['date']) ) );
        }

//        $xml_string = str_replace('src="','src="'.$this->config->get('siteurl'), $xml_string);
        //$xml_string = htmlspecialchars_decode( $xml_string );

        if ( ! defined('TEST') && $this->config->get('debug.profiler') ) {
            $dom->formatOutput = true;
        }
        return $dom->saveXML();
    }
}
