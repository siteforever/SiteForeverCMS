<?php
namespace Module\System\Test;

use Sfcms\Test\WebCase;

/**
 * 
 * @author: keltanas <keltanas@gmail.com>
 */
class CaptchaControllerTest extends WebCase
{
    public function testIndexAction()
    {
        ob_start();
        $response = $this->runRequest('/?controller=captcha');
        ob_end_clean();
        $this->assertEquals('image/png', $response->headers->get('content-type'));
    }
}
