<?php
use Sfcms\View\Breadcrumbs;
/**
 * Test class for View_Breadcrumbs.
 * Generated by PHPUnit on 2011-05-24 at 18:09:44.
 */
class View_BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Breadcrumbs
     */
    protected $object;

    protected $pathes = array(
        array(
            'name' => 'Главная',
            'url'  => '',
        ),
        array(
            'name' => 'О компании',
            'url'  => 'about',
        ),
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Breadcrumbs;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @return void
     */
    public function testRender()
    {
        $ser = serialize( $this->pathes );

        $this->object->fromSerialize( $ser );

        $this->assertEquals( '<ul class="breadcrumb"><li><a href="/">Главная</a><span class="divider">&gt;</span></li><li><a href="/about">О компании</a></li></ul>', $this->object->render() );
    }

    public function testRenderJson()
    {
        $ser = json_encode( $this->pathes );

        $this->object->fromJson( $ser );

        $this->assertEquals( '<ul class="breadcrumb"><li><a href="/">Главная</a><span class="divider">&gt;</span></li><li><a href="/about">О компании</a></li></ul>', $this->object->render() );
    }

    public function testAddPiece()
    {
        $this->object->addPiece( '', 'Главная' );
        $this->object->addPiece( 'about', 'О компании' );

        $this->assertEquals( '<ul class="breadcrumb"><li><a href="/">Главная</a><span class="divider">&gt;</span></li><li><a href="/about">О компании</a></li></ul>', $this->object->render() );
    }

}
