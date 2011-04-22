<?php
/**
 * Контроллер RSS лент
 * @author keltanas aka Nikolay Ermin
 */

class Controller_Rss extends Controller
{
    function indexAction()
    {
        $this->request->setAjax(true, Request::TYPE_XML);
        $this->request->setTemplate('inner');

        // @TODO Нет абсолютного подтверждения работы этого модуля
        /**
         * @var Model_News $model
         */
        $model = $this->getModel('News');

        $criteria   = new Db_Criteria(array(
            'cond'      => ' hidden = 0 AND protected = 0 AND deleted = 0 ',
            'params'    => array(),
            'order'     => 'date DESC',
            'limit'     => 20,
        ));

        //$crit   = ;


        $news   = $model->findAllWithLinks( $criteria );

        $this->tpl->assign('data', $news);
        $this->tpl->assign('gmdate', gmdate('D, d M Y H:i:s', time()).' GTM');

        $this->request->set('getcontent', true);

        //header('Content-type: text/xml; charset=utf-8');

        $rss    = new SimpleXMLElement('<rss />', null );
        $rss->addAttribute('version', '2.0');
        $channel= $rss->addChild('channel');

        /**
         * @var SimpleXMLElement $item
         */

        $channel->addChild('title', $this->config->get('sitename'));
        $channel->addChild('link', $this->config->get('siteurl'));
        $channel->addChild('description', $this->config->get('sitename'));

//        $channel->addChild('pubDate', date('r'));
//        $channel->addChild('lastBuildDate', date('r'));
//        $channel->addChild('docs',$this->config->get('siteurl').'/rss');
        $channel->addChild('generator','SiteForeverCMS');
//        $channel->addChild('managingEditor',$this->config->get('admin'));
//        $channel->addChild('webMaster', 'nikolay@ermin.ru');
        
        foreach( $news as $n ) {
            $description = $n['notice'];
            $description = str_replace('&nbsp;', ' ', $description);

            $item = $channel->addChild('item');
            $item->addChild('title', $n['title'] ? $n['title'] : $n['name']);
            $item->addChild('link', $this->config->get('siteurl').$this->router->createLink($n['alias'], array('doc'=>$n['id'])));
            $item->addChild('description', $description );
            $item->addChild('pubDate', date('r', $n['date']));
        }

        $xml_string = $rss->asXML();

        $xml_string = str_replace('src="','src="'.$this->config->get('siteurl'), $xml_string);
        //$xml_string = htmlspecialchars_decode( $xml_string );

        $this->request->setContent($xml_string);
        return;
    }
}
