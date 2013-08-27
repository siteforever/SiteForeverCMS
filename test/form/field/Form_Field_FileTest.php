<?php
/**
 * Testing form file field
 */

use Sfcms\Request;
use Module\Test\Form\TestFileForm;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Form_Field_FileTest extends PHPUnit_Framework_TestCase
{
    /** @var TestFileForm */
    protected $form;

    protected function setUp()
    {
        parent::setUp();
        $this->form = new TestFileForm();
    }

    public function testSendFile()
    {
        $request = Request::create('/send',
            'POST',
            array('test' => array('name' => 'hello',),),
            array(),
            array(
                'test' => array(
                    'name' => array('file' => 'IMAG0497.jpg',),
                    'type' => array('file' => 'image/jpeg',),
                    'tmp_name' => array('file' => realpath(__DIR__ . '/../../fixtures/IMAG0497.jpg'),),
                    'error' => array('file' => 0,),
                    'size' => array('file' => 1539839,),
                ),
            )
        );

        $this->form->getPost($request);
        $this->form->validate();
        $this->assertCount(0, $this->form->getErrors());
        $this->assertTrue($this->form->file instanceof UploadedFile);
    }
}