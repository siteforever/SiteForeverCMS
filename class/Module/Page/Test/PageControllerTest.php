<?php
/**
 * Pages test
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Test;

use Sfcms\Test\TestCase;

class PageControllerTest extends TestCase
{
    public function testDeleteAction()
    {
        $this->session->set('user_id', 1);

        $this->request->setAjax(true, 'json');
        $this->request->query->set('id', 62);
        $response = $this->runController('page', 'delete');
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent());
        $this->assertEquals(0, $json->error);
        $this->assertEquals('ok', $json->msg);
        $this->assertEquals(62, $json->id);
    }
}
