<?php
namespace Module\Guestbook\Form;

use Sfcms\Form\Form;
use Sfcms\Router;

/**
 * Редактирование сообщения в гостевой
 * @author: keltanas <keltanas@gmail.com>
 */
class EditForm extends Form
{
    public function __construct()
    {
        parent::__construct(array(
            'name'  => 'guestbook_edit',
            'action' => \App::cms()->getRouter()->createServiceLink('guestbook','edit'),
            'fields'=> array(
                'id'    => array( 'type'=>'hidden', 'required' ),
//                'name'  => array( 'type'=>'text', 'label'=>t('guestbook','Name'), ),
//                'email'  => array( 'type'=>'text', 'label'=>t('guestbook','Email'), ),
//                'site'  => array( 'type'=>'text', 'label'=>t('guestbook','Site'), ),
//                'city'  => array( 'type'=>'text', 'label'=>t('guestbook','City'), ),
//                'date'  => array( 'type'=>'date', 'label'=>t('guestbook','Date'), ),
//                'ip'  => array( 'type'=>'text', 'readonly', 'label'=>t('guestbook','Ip'), ),

                'message'  => array( 'type'=>'textarea', 'label'=>'Message', 'class'=>'plain', ),
                'answer'  => array( 'type'=>'textarea', 'label'=>'Answer', 'class'=>'plain', ),

                'hidden'     => array(
                    'type'      => 'radio',
                    'label'     => 'Скрывать',
                    'value'     => '0',
                    'variants'  => array('No', 'Yes'),
                ),

                'submit'    => array( 'type'=>'submit', 'value'=>'Save'),
            ),
        ) );
    }

}
