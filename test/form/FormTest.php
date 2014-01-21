<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

use Sfcms\Form\Form;

class form_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $form;

    protected function setUp()
    {
        $this->form = new Form(array(
            'name'  => 'test',
        ));
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
}
