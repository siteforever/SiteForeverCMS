<?php
/**
 * Created by JetBrains PhpStorm.
 * User: keltanas
 * Date: 27.08.13
 * Time: 19:02
 * To change this template use File | Settings | File Templates.
 */

namespace Module\Test\Form;

use Sfcms\Form\Form;

class TestFileForm extends Form
{
    public function __construct()
    {
        parent::__construct(array(
            'name' => 'test',
            'fields' => array(
                'name' => array('type'=>'text', 'label'=>'Your name'),
                'file' => array('type'=>'file', 'label'=>'choose you file', 'mime' => ['image/jpeg']),
                'submit' => array('type'=>'submit', 'value'=>'send file'),
            ),
        ));
    }

}
