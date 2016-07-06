<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Form;

use Sfcms\Form\Exception;
use Sfcms\Form\Form;
use Sfcms\i18n;
use Sfcms\Request;

class CommentForm extends Form
{
    public function __construct($options = array(), i18n $i18n, Request $request = null)
    {
        parent::__construct(array(
            'name'  => 'comment',
            'class' => 'form-horizontal ajax',
            'action' => '#product_comments',
//            'action'=> \App::getInstance()->getRouter()->createServiceLink('catalog','comment'),
                'fields' => array(
                    'product_id' => array('type' => 'hidden'),
                    'name' => array(
                        'type' => 'text',
                        'label' => $i18n->write('catalog', 'You name'),
                        'required',
                    ),
                    'email' => array(
                        'type' => 'text',
                        'filter' => 'email',
                        'label' => $i18n->write('catalog', 'Email'),
                    ),
                    'phone' => array(
                        'type' => 'text',
                        'filter' => 'phone',
                        'label' => $i18n->write('catalog', 'Phone')
                    ),
                    'subject' => array('type' => 'text', 'label' => $i18n->write('catalog', 'Subject')),
                    'content' => array(
                        'type' => 'textarea',
                        'label' => $i18n->write('catalog', 'Message'),
                        'required',
                    ),
                    'captcha' => array('type' => 'captcha', 'label' => $i18n->write('catalog', 'Captcha')),
                    'submit' => array('type' => 'submit', 'value' => $i18n->write('catalog', 'Send')),
                ),
        ), $request);
    }
}
