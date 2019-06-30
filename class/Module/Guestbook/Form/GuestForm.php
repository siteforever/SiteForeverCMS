<?php
namespace Module\Guestbook\Form;

use Sfcms\Form\Form;
use Sfcms\Router;

/**
 * Форма сообщения в гостевой
 * @author: keltanas <keltanas@gmail.com>
 */
class GuestForm extends Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name'  => 'guestbook',
            'method' => 'post',
            'fields'=> array(
                'name'  => array('type'=>'text', 'label'=>'Name', 'required'),
                'email'  => array('type'=>'text', 'label'=>'Email', 'required',
                    'filter'    => 'email',
                ),
                'message'  => array('type'=>'textarea', 'label'=>'Message', 'required'),
                'captcha' => array('type'=>'captcha', 'label'=>'Captcha',),
                'submit'    => array('type'=>'submit', 'class'=>'btn-success', 'value'=>'Send'),
            ),
        ));
    }
}
