<?php
/**
 * Тестирует контроллер каталога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Test;

use Sfcms\Test\TestCase;

class CatalogControllerTest extends TestCase
{

    public function testIndexAction()
    {
        $response = $this->runRequest('/catalog/velosipedy');
        var_dump($response->getContent());
    }
}
