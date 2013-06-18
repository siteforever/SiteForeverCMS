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
                'name'  => array('type'=>'text', 'label'=>$this->t('guestbook','Name'), 'required'),
                'email'  => array('type'=>'text', 'label'=>$this->t('guestbook','Email'), 'required',
                    'filter'    => 'email',
                ),
                'message'  => array('type'=>'textarea', 'label'=>$this->t('guestbook','Message'), 'required'),
                'captcha' => array('type'=>'captcha', 'label'=>$this->t('guestbook','Captcha')),
                'submit'    => array('type'=>'submit', 'value'=>$this->t('guestbook','Send')),
            ),
        ) );
    }
}
