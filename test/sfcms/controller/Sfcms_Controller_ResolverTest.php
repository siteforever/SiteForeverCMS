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

    /** @var Sfcms\Request */
    public $request = null;

    protected function setUp()
    {
        $this->resolver = App::getInstance()->getResolver();
        $this->request = App::getInstance()->getRequest();
    }


    /**
     * Решаем, какой контроллер в запросе с модулем
     */
    public function testResolveController()
    {
        $this->request->clearAll();
        $result = $this->resolver->resolveController('page');
        $this->assertEquals('Module\Page\Controller\PageController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $this->request->clearAll();
        $this->request->setController('page');
        $this->request->setAction('edit');
        $result = $this->resolver->resolveController();
        $this->assertEquals('Module\Page\Controller\PageController', $result['controller']);
        $this->assertEquals('editAction', $result['action']);

        $this->request->clearAll();
        $result = $this->resolver->resolveController('search');
        $this->assertEquals('Controller_Search', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $this->request->clearAll();
        $this->request->setController('foo');
        $result = $this->resolver->resolveController();
        $this->assertEquals('Acme\Module\Foo\Controller\FooController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $this->request->clearAll();
        $result = $this->resolver->resolveController('foo');
        $this->assertEquals('Acme\Module\Foo\Controller\FooController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $this->request->clearAll();
        $result = $this->resolver->resolveController('foo','index','foo');
        $this->assertEquals('Acme\Module\Foo\Controller\FooController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);
        $this->assertEquals('Foo', $result['module']);

//        $this->assertArrayHasKey('module', $result);
//        $this->assertArrayHasKey('controller', $result);
//        $this->assertArrayHasKey('action', $result);
    }
}
