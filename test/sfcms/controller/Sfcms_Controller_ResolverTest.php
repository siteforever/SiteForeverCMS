<?php
/**
 * Тестирует резолвер контроллеров
 * @author: keltanas
 * @link  http://siteforever.ru
 */
class Sfcms_Controller_ResolverTest extends PHPUnit_Framework_TestCase
{
    /** @var Sfcms\Controller\Resolver */
    public $resolver = null;

    protected function setUp()
    {
        $this->resolver = App::getInstance()->getResolver();
    }


    /**
     * Решаем, какой контроллер в запросе с модулем
     */
    public function testResolveController()
    {
//        $this->markTestSkipped();
//        $result = $this->resolver->resolveController('module','save','system');
//        $this->assertInternalType('array',$result);
//        $this->assertArrayHasKey('module', $result);
//        $this->assertArrayHasKey('controller', $result);
//        $this->assertArrayHasKey('action', $result);
    }
}
