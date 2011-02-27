<?php
/**
 * Контроллер RSS лент
 * @author keltanas aka Nikolay Ermin
 */

class controller_Rss extends Controller
{
    function indexAction()
    {
        $this->request->setAjax(true, Request::TYPE_XML);

        // @TODO Нет абсолютного подтверждения работы этого модуля
        /**
         * @var Model_News $model
         */
        $model = $this->getModel('News');

        $crit   = array(
            'cond'      => ' hidden = 0 AND protected = 0 AND deleted = 0 ',
            'params'    => array(),
            'limit'     => 10,
        );


        $news   = $model->findAllWithLinks( $crit );

        $this->tpl->assign('data', $news);
        $this->tpl->assign('gmdate', gmdate('D, d M Y H:i:s', time()).' GTM');

        $this->request->set('getcontent', true);

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



        foreach( $news as $n ) {
            $item = $channel->addChild('item');
            $item->addChild('title', $n['title']);
            $item->addChild('link', $this->config->get('siteurl').$this->router->createLink($n['link'], array('doc'=>$n['id'])));
            $item->addChild('description', $n['notice']);
            $item->addChild('pubDate', date('r', $n['date']));
        }


        $this->request->setContent( $rss->asXML() );
    }
}
