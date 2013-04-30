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
        $this->resolver = new \Sfcms\Controller\Resolver();
        $this->request = \Sfcms\Request::create('/');
    }


    /**
     * Решаем, какой контроллер в запросе с модулем
     */
    public function testResolveController()
    {
        $request = $this->request;
        $request->setController('page');
        $result = $this->resolver->resolveController($request);
        $this->assertEquals('Module\Page\Controller\PageController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $this->request->setController('page');
        $this->request->setAction('edit');
        $result = $this->resolver->resolveController($request);
        $this->assertEquals('Module\Page\Controller\PageController', $result['controller']);
        $this->assertEquals('editAction', $result['action']);

        $result = $this->resolver->resolveController($request, 'search', 'index');
        $this->assertEquals('Controller_Search', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $this->request->setController('foo');
        $this->request->setAction('index');
        $result = $this->resolver->resolveController($request);
        $this->assertEquals('Acme\Module\Foo\Controller\FooController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);

        $result = $this->resolver->resolveController($request, 'foo','index','foo');
        $this->assertEquals('Acme\Module\Foo\Controller\FooController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);
        $this->assertEquals('Foo', $result['module']);

        $request->setModule('foo');
        $request->setController('foo');
        $request->setAction('index');
        $result = $this->resolver->resolveController($request);
        $this->assertEquals('Acme\Module\Foo\Controller\FooController', $result['controller']);
        $this->assertEquals('indexAction', $result['action']);
    }
}
