<?php
/**
 * Форма сообщения в гостевой
 * @author: keltanas <keltanas@gmail.com>
 */
class Forms_Guestbook_Form extends Form_Form
{
    function __construct()
    {
        return parent::__construct( array(
            'name'  => 'guestbook',
            'fields'=> array(
                'name'  => array('type'=>'text', 'label'=>t('Name'), 'required'),
                'email'  => array('type'=>'text', 'label'=>t('Email'), 'required',
                    'filter'    => '/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/',
                ),
                'message'  => array('type'=>'textarea', 'label'=>t('Message'), 'required'),
                'captcha' => array('type'=>'captcha', 'label'=>t('Captcha')),
                'submit'    => array('type'=>'submit', 'value'=>t('Send')),
            ),
        ) );
    }
}
