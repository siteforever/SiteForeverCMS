<?php
/**
 * Контроллер RSS лент
 * @author keltanas aka Nikolay Ermin
 */

class controller_Rss extends Controller
{
    function indexAction()
    {
        // @TODO Нет абсолютного подтверждения работы этого модуля
        /**
         * @var model_news $model
         */
        $model = $this->getModel('news');

        $model->setCond('news.hidden = 0 AND news.protected = 0 AND news.deleted = 0');
        $data = $model->findAllWithLinks(10);

        //printVar($data);

        $this->tpl->assign('data', $data);
        $this->tpl->assign('gmdate', gmdate('D, d M Y H:i:s', time()).' GTM');

        $this->request->set('getcontent', true);
        $this->setAjax();

        //header('Content-type: text/xml; charset=utf-8');

        $rss    = new SimpleXMLElement('<rss />');
        $rss->addAttribute('version', '2.0');
        $channel= $rss->addChild('channel');

        /**
         * @var SimpleXMLElement $item
         */

        $channel->addChild('title', $this->config->get('sitename'));
        $channel->addChild('link', $this->config->get('siteurl'));
        $channel->addChild('pubDate', date('r'));
        $channel->addChild('lastBuildDate', date('r'));
        $channel->addChild('docs',$this->config->get('siteurl').'/rss');
        $channel->addChild('generator','SiteForeverCMS');
        $channel->addChild('managingEditor',$this->config->get('admin'));
        $channel->addChild('webMaster', 'nikolay@ermin.ru');



        foreach( $data as $news ) {
            $item = $channel->addChild('item');
            $item->addChild('title', $news['title']);
            $item->addChild('link', $this->config->get('siteurl').$this->router->createLink($news['link'], array('doc'=>$news['id'])));
            $item->addChild('description', $news['notice']);
            $item->addChild('pubDate', date('r', $news['date']));
        }


        $this->request->setContent( $rss->asXML() );
    }
}
