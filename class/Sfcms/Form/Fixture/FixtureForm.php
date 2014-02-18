<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Sfcms\Form\Fixture;

use Sfcms\Form\Form;

class FixtureForm extends Form
{
    public function __construct()
    {
        parent::__construct(array(
                'name' => 'fixture',
                'fields' => array(
                    'id' => array('type'=>'hidden'),
                    'name' => array('type'=>'text', 'label'=>'Name', 'notice'=>'What is your name?'),
                    'email' => array('type'=>'text', 'label'=>'Email', 'filter'=>'email'),
                    'password' => array('type'=>'password', 'label'=>'Password'),
                    'phone' => array('type'=>'text', 'label'=>'Phone', 'filter'=>'phone'),
                    'birthday' => array('type'=>'date', 'label'=>'Birthday'),
                    'upload' => array('type'=>'file', 'label'=>'Choose your file'),
                    'check' => array('type'=>'checkbox', 'label'=>'Remember me'),
                    'radio' => array('type'=>'radio', 'value'=>'0', 'variants'=>array(
                        '0' => 'First',
                        '1' => 'Second',
                        '2' => 'Third',
                    )),
                    'select' => array('type'=>'select', 'value'=>'0', 'variants'=>array(
                        '0' => 'First',
                        '1' => 'Second',
                        '2' => 'Third',
                    )),
                    'info' => array('type'=>'textarea', 'label'=>'Info'),
                    'submit' => array('type'=>'submit', 'value'=>'Send'),
                ),
            ));
    }
}
