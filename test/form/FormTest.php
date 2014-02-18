<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */
use Sfcms\Form\Form;
use Sfcms\Form\Fixture\FixtureForm;
use Symfony\Component\DomCrawler\Crawler;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $form;

    protected function setUp()
    {
        $this->form = new FixtureForm();
    }


    public function testCreateField()
    {
        $field  = $this->form->createField(array(
            'name' => 'id',
            'type' => 'int',
            'label'=> 'Identifier'
        ));

        try {
            $this->assertEquals(null, $this->form->getChild('id'));
        } catch ( Exception $e ) {
            $this->form->setChild( $field );
            $this->assertInstanceOf('Sfcms\Form\FormFieldAbstract', $this->form->getChild('id'));
            return;
        }
        $this->fail('Expected exception');
    }

    public function testHtml()
    {
        $content = $this->form->createView()->html(array('hint'=>true, 'buttons'=>true));
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);
        $this->assertEquals(1, $crawler->filter('form#form_fixture')->count());
        $this->assertEquals(13, $crawler->filter('input')->count());
        $this->assertEquals('hidden', $crawler->filter('#fixture_id')->attr('type'));
        $this->assertEquals('text', $crawler->filter('#fixture_name')->attr('type'));
        $this->assertEquals('text', $crawler->filter('#fixture_email')->attr('type'));
        $this->assertEquals('password', $crawler->filter('#fixture_password')->attr('type'));
        $this->assertEquals('text', $crawler->filter('#fixture_phone')->attr('type'));
        $this->assertEquals('text', $crawler->filter('#fixture_birthday')->attr('type'));
        $this->assertEquals('date datepicker', $crawler->filter('#fixture_birthday')->attr('class'));
        $this->assertEquals('file', $crawler->filter('#fixture_upload')->attr('type'));
        $this->assertEquals('checkbox', $crawler->filter('#fixture_check')->attr('type'));
        $this->assertEquals('radio', $crawler->filter('input[type=radio]:checked')->attr('type'));
        $this->assertEquals(3, $crawler->filter('select#fixture_select')->filter('option')->count());
        $this->assertEquals('textarea', $crawler->filter('#fixture_info')->attr('type'));
        $this->assertEquals('submit', $crawler->filter('#fixture_submit')->attr('type'));
        $this->assertEquals('Send', $crawler->filter('#fixture_submit')->attr('value'));
    }
}
