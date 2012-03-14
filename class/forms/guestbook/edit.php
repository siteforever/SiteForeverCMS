<?php
/**
 * Редактирование сообщения в гостевой
 * @author: keltanas <keltanas@gmail.com>
 */
class Forms_Guestbook_Edit extends Form_Form
{
    function __construct()
    {
        parent::__construct( array(
            'name'  => 'guestbook_edit',
            'fields'=> array(
                'id'    => array( 'type'=>'hidden' ),
                'name'  => array( 'type'=>'text', 'label'=>t('Name'), ),
                'email'  => array( 'type'=>'text', 'label'=>t('Email'), ),
                'site'  => array( 'type'=>'text', 'label'=>t('Site'), ),
                'city'  => array( 'type'=>'text', 'label'=>t('City'), ),
                'date'  => array( 'type'=>'date', 'label'=>t('Date'), ),
                'ip'  => array( 'type'=>'text', 'readonly', 'label'=>t('Ip'), ),

                'message'  => array( 'type'=>'textarea', 'label'=>t('Message'), ),
                'submit'    => array( 'type'=>'submit', 'value'=>t('Save') ),
            ),
        ) );
    }

}
