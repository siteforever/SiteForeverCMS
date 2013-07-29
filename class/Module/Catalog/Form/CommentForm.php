<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Form;

use Sfcms\Form\Exception;
use Sfcms\Form\Form;
use Sfcms\Request;

class CommentForm extends Form
{
    public function __construct($config = array(), Request $request = null)
    {
        parent::__construct(array(
            'name'  => 'comment',
            'class' => 'form-horizontal ajax',
//            'action' => '#product_comments',
//            'action'=> \App::getInstance()->getRouter()->createServiceLink('catalog','comment'),
            'fields'=> array(
                'product_id' => array('type'=>'hidden'),
                'name' => array('type'=>'text', 'label'=>\Sfcms::i18n()->write('catalog', 'You name'), 'required',),
                'email' => array('type'=>'text', 'filter'=>'email', 'label'=>\Sfcms::i18n()->write('catalog', 'Email'), 'required',),
                'phone' => array('type'=>'text', 'filter'=>'phone','label'=>\Sfcms::i18n()->write('catalog', 'Phone')),
                'subject' => array('type'=>'text', 'label'=>\Sfcms::i18n()->write('catalog', 'Subject')),
                'content' => array('type'=>'textarea', 'label'=>\Sfcms::i18n()->write('catalog', 'Message'), 'required',),
                'captcha' => array('type'=>'captcha', 'label'=>\Sfcms::i18n()->write('catalog', 'Captcha')),
                'submit'    => array('type'=>'submit', 'value'=>\Sfcms::i18n()->write('catalog', 'Send')),
            ),
        ), $request);
    }
}
