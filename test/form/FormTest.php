<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Form_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Form_Form
     */
    protected $form;

    protected function setUp()
    {
        $this->form = new Form_Form( array(
                'name'  => 'test',
           ), App::getInstance()->getRequest() );
    }


    public function testCreateField()
    {
        $field  = $this->form->createField('id',
            array(
                 'type' => 'int',
                 'label'=> 'Идентификатор'
            )
        );

        try {
            $this->assertEquals( null, $this->form->getField( 'id' ) );
        } catch ( Form_Exception $e ) {
            $this->form->addField( $field );
            $this->assertInstanceOf('Form_Field', $this->form->getField('id'));
            return;
        }
        $this->fail('Expected exception');
    }
}
