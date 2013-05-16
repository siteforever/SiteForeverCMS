<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

use Sfcms\Form\Form;

class form_FormTest extends \Sfcms\Test\TestCase
{
    /**
     * @var Form
     */
    protected $form;

    protected function setUp()
    {
        $this->form = new Form( array(
                'name'  => 'test',
        ), $this->request);
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
        } catch ( Exception $e ) {
            $this->form->addField( $field );
            $this->assertInstanceOf('\Sfcms\Form\Field', $this->form->getField('id'));
            return;
        }
        $this->fail('Expected exception');
    }
}
