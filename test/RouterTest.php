<?php

/**
 * Test class for Router.
 * Generated by PHPUnit on 2011-02-16 at 17:04:19.
 */
use Sfcms\Router;
use Sfcms\Request;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;

    /** @var \Sfcms\Config */
    protected $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->request = Request::create('/');
        $this->router  = new Router($this->request);
        $this->config  = App::getInstance()->getConfig();
        $this->config->set('url.rewrite', true);
    }

    public function testFilterEqParams()
    {
        $this->assertEquals(
            'news/view',
            $this->router->filterEqParams('news/view/doc=35/page=10')
        );
    }


    /**
     * @todo Implement testCreateLink().
     */
    public function testCreateLink()
    {
        $this->config->set('url.rewrite', false);

        $url    = 'example/foo';
        $params = array(
            'par1' => 'val1',
            'par2' => 'val2'
        );

        $this->assertEquals(
            "/?route={$url}&par1=val1&par2=val2",
            $this->router->createLink($url, $params)
        );

        $this->config->set('url.rewrite', true);

        $this->assertEquals(
            "/{$url}/par1=val1/par2=val2",
            $this->router->createLink($url, $params)
        );
    }

    public function testCreateLink2()
    {
        $this->assertEquals(
            '/',
            $this->router->createLink(null)
        );
    }

    public function testCreateLink3()
    {
        $this->assertEquals(
            '/',
            $this->router->createLink('/')
        );
    }

    public function testCreateLinkCatalog()
    {
        $this->assertEquals(
            '/catalog/id=50/page=2',
            $this->router->createLink('catalog', array('id' => 50, 'page' => 2))
        );
    }

    public function testCreateServiceLink2()
    {
        $this->assertEquals(
            '/',
            $this->router->createServiceLink('index')
        );
    }

    public function testCreateLinkZendStyle()
    {
        $this->assertEquals(
            '/page/create/id/123/page/7',
            $this->router->createLink(
                null,
                array(
                    'controller' => 'page',
                    'action'     => 'create',
                    'id'         => 123,
                    'page'       => 7,
                )
            )
        );
    }

    public function testCreateServiceLinkPage()
    {
        $this->assertEquals(
            '/page',
            $this->router->createServiceLink('page')
        );
    }

    public function testCreateServiceLinkPage2()
    {
        $this->assertEquals(
            '/page/edit',
            $this->router->createServiceLink('page', 'edit')
        );
    }

    public function testCreateServiceLinkPageNorewrite()
    {
        $this->config->set('url.rewrite', false);
        $this->assertEquals(
            '/?route=page',
            $this->router->createServiceLink('page')
        );
    }

    public function testCreateServiceLinkPage2Norewrite()
    {
        $this->config->set('url.rewrite', false);
        $this->assertEquals(
            '/?route=page/edit',
            $this->router->createServiceLink('page', 'edit')
        );
    }

    /**
     */
    public function testFindRoute()
    {
        // find route in routes.xml
        $this->router->setRoute('page/nameconvert/id/123/page/7')->routing(true);
        $this->assertEquals($this->request->getController(), 'page');
        $this->assertEquals($this->request->getAction(), 'nameconvert');

        $this->assertEquals('7', $this->request->get('page'));
        $this->assertEquals('123', $this->request->get('id'));
    }

    public function testFindRouteAdminUsers()
    {
        $this->router->setRoute('users/admin')->routing(true);

        $this->assertEquals('users', $this->request->getController());
        $this->assertEquals('admin', $this->request->getAction());
    }

    public function testFindRouteNews()
    {
        $this->router->setRoute('news/doc=35')->routing(true);
        $this->assertEquals($this->request->getController(), 'news');
        $this->assertEquals($this->request->getAction(), 'index');

        $this->assertEquals('35', $this->request->get('doc'));
        $this->assertEquals('2', $this->request->get('pageid'));
    }

    public function testFindRouteUsersCabinet()
    {
        $this->router->setRoute('users/cabinet')->routing(true);
        $this->assertEquals($this->request->getController(), 'users');
        $this->assertEquals($this->request->getAction(), 'cabinet');
    }

    public function testFindRouteAdmin()
    {
        $this->router->setRoute('admin');
        $this->router->routing(true);
        $this->assertEquals('page', $this->request->getController());
        $this->assertEquals('admin', $this->request->getAction());
    }

    public function testFindRouteUsersEdit()
    {
        $this->router->setRoute('users/edit');
        $this->router->routing(true);
        $this->assertEquals($this->request->getController(), 'users');
        $this->assertEquals($this->request->getAction(), 'edit');
    }

    public function testFindRouteBasket()
    {
        $this->router->setRoute('basket');
        $this->router->routing(true);
        $this->assertEquals($this->request->getController(), 'basket');
        $this->assertEquals($this->request->getAction(), 'index');
    }

    public function testFindRouteTest()
    {
        $this->router->setRoute('test');
        $this->router->routing(true);
        $this->assertEquals($this->request->getController(), 'test');
        $this->assertEquals($this->request->getAction(), 'test');
    }

    public function testFindRouteIndex()
    {
        $this->router->setRoute('index');
        $this->router->routing(true);
    }

    public function testFindRouteCatalogWithId()
    {
        $this->router->setRoute('someroute/id=3');
        $this->router->routing(true);
        $this->assertEquals('3', $this->request->get('id'));
    }

    public function testCreateLinkCatGallery()
    {
        $url = $this->router->createLink(
            '',
            array(
                'controller' => 'catgallery',
                'id'         => 5
            )
        );
        $this->assertEquals('/catgallery/index/id/5', $url);
    }


    public function testGetSetRoute()
    {
        $this->router->setRoute('index');
        $this->assertEquals('index', $this->router->getRoute());
    }

    public function testNewsOnly()
    {
        $this->router->setRoute('news');
        $this->router->routing(true);
        $this->assertEquals('news', $this->request->getController());
        $this->assertEquals('index', $this->request->getAction());
        $this->assertEquals('2', $this->request->get('pageid'));
    }

    public function testNewsRoute()
    {
        $this->router->setRoute('news/edit/id/10/page/5');
        $this->router->routing(true);
        $this->assertEquals('news', $this->request->getController());
        $this->assertEquals('edit', $this->request->getAction());
        $this->assertEquals('10', $this->request->get('id'));
        $this->assertEquals('5', $this->request->get('page'));
    }

    public function testNewsRouteByAlias()
    {
        $this->router->setRoute('news/2-novostj-2');
        $this->router->routing(true);
        $this->assertEquals('news', $this->request->getController());
        $this->assertEquals('2-novostj-2', $this->request->get('alias'));
        $this->assertEquals('inner', $this->request->getTemplate());
    }

    public function testNewsRouteByAlias2()
    {
        $this->router->setRoute('blog/4-moya-pervaya-statjya');
        $this->router->routing(true);
        $this->assertEquals('news', $this->request->getController());
        $this->assertEquals('4-moya-pervaya-statjya', $this->request->get('alias'));
    }

    public function testCatalogRoute()
    {
        $this->router->setRoute('catalog/HTC-Evo-3D');
        $this->router->routing(true);
        $this->assertEquals('catalog', $this->request->getController());
        $this->assertEquals('HTC-Evo-3D', $this->request->get('alias'));
    }

    public function testFooModule()
    {
        $this->router->setRoute('foo');
        $this->router->routing(true);
        $this->assertEquals('foo', $this->request->getController());
        $this->assertEquals('index', $this->request->getAction());
    }

    public function testServiceLinkGrid()
    {
        $this->assertEquals('/goods/grid', $this->router->createServiceLink('goods', 'grid'));
    }

    /**
     * Маршрут включая модуль
     */
    public function testHaveModule()
    {
        //        $this->markTestSkipped();
        //        $this->router->setRoute('system/module/save');
        //        $this->router->routing(true);
        //        $this->assertEquals('system',$this->request->getModule());
        //        $this->assertEquals('module',$this->request->getController());
        //        $this->assertEquals('save',$this->request->getAction());
    }
}
