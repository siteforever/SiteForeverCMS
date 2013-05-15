<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Forms\User;

use Sfcms\Form\Form;
use Sfcms\Request;

class Restore extends Form
{
    public function __construct( $config = array(), Request $request = null )
    {
        parent::__construct( array(
            'name' => 'restore',
            'action' => \Sfcms::html()->url('user/restore'),
            'fields' => array(
                'email' => array(
                    'type' => 'text',
                    'label' => t('Email'),
                    'filter' => 'email',
                    'required',
                ),
                'captcha' => array(
                    'type' => 'captcha',
                    'label' => 'Код с картинки'
                ),
                'submit' => array(
                    'type' => 'submit',
                    'value' => 'Отправить',
                ),
            ),

        ), $request );
    }

}
