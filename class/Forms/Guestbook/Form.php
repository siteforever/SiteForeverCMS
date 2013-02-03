<?php
/**
 * Форма сообщения в гостевой
 * @author: keltanas <keltanas@gmail.com>
 */
class Forms_Guestbook_Form extends \Sfcms\Form\Form
{
    public function __construct()
    {
        return parent::__construct( array(
            'name'  => 'guestbook',
            'method' => 'post',
            'fields'=> array(
                'name'  => array('type'=>'text', 'label'=>t('guestbook','Name'), 'required'),
                'email'  => array('type'=>'text', 'label'=>t('guestbook','Email'), 'required',
                    'filter'    => 'email',
                ),
                'message'  => array('type'=>'textarea', 'label'=>t('guestbook','Message'), 'required'),
                'captcha' => array('type'=>'captcha', 'label'=>t('guestbook','Captcha')),
                'submit'    => array('type'=>'submit', 'value'=>t('guestbook','Send')),
            ),
        ) );
    }
}
