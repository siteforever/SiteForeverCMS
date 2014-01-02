<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Test;

use Sfcms\Test\WebCase;
use Symfony\Component\DomCrawler\Crawler;

class ElfinderControllerTest extends WebCase
{
    public function testFinderAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runController('Elfinder', 'finder');
        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getContent());
        $finder = $crawler->filterXPath('//div[@id="elfinder"]');
        $this->assertEquals('', $finder->text());
    }

    public function testConnectorAction()
    {
//        $this->session->set('user_id', 1);
//        http://cms.sf/?route=elfinder/connector&cmd=open&target=c7b2ae9320ea9a12cdc0036dc48ee974&init=true&tree=true&_=1368733427812
//        $_GET = array(
//            'cmd' => 'open',
//            'target' => 'c7b2ae9320ea9a12cdc0036dc48ee974',
//            'init' => 'true',
//            'tree' => 'true',
//            '_'    => '1368733427812',
//        );
//        $response = $this->runController('Elfinder', 'connector');
//        $this->assertEquals('application/json', $response->headers->get('content-type'));
//        $json = json_decode($response->getContent(), true);
//        $this->assertInternalType('array', $json);
    }
}
