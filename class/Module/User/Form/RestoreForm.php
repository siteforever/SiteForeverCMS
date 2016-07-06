<?php
/**
 * Restore password form
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\User\Form;

use Sfcms\Form\Form;

class RestoreForm extends Form
{
    public function __construct($options = array())
    {
        parent::__construct( array(
            'name' => 'restore',
            'action' => \App::cms()->get('router')->generate('user/restore'),
            'fields' => array(
                'email' => array(
                    'type' => 'text',
                    'label' => 'Email',
                    'filter' => 'email',
                    'required',
                ),
                'captcha' => array(
                    'type' => 'captcha',
                    'label' => 'Captcha'
                ),
                'submit' => array(
                    'type' => 'submit',
                    'value' => 'Send',
                ),
            ),

        ));
    }
}
