<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Test;

use Sfcms\Test\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class ElfinderControllerTest extends TestCase
{
    public function testFinderAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runController('Elfinder', 'finder');
        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getContent());
        $finder = $crawler->filter('#finder');
        $this->assertEquals('finder', $finder->text());
    }

    public function testConnectorAction()
    {
        $this->session->set('user_id', 1);
        http://cms.sf/?controller=elfinder&action=connector&cmd=open&target=c7b2ae9320ea9a12cdc0036dc48ee974&init=true&tree=true&_=1368733427812
        $_GET = array(
            'cmd' => 'open',
            'target' => 'c7b2ae9320ea9a12cdc0036dc48ee974',
            'init' => 'true',
            'tree' => 'true',
            '_'    => '1368733427812',
        );
        $response = $this->runController('Elfinder', 'connector');
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $json);
    }
}
