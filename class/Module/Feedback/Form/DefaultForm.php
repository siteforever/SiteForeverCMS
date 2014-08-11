<?php
/**
 * Форма обратной связи по умолчанию
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Module\Feedback\Form;

use Sfcms\Form\Form;

class DefaultForm extends Form
{
    public function __construct()
    {
        return parent::__construct( array(
            'name'      => 'feedback',
            'enctype'   => 'multipart/form-data',
            'fields'    => array(
                'name'      => array(
                    'type'      => 'text',
                    'label'     => 'You name',
                    'required',
                ),
                'email'     => array(
                    'type'      => 'text',
                    'label'     => 'You email',
                    'filter'    => 'email',
                    'required',
                ),
                'title'     => array(
                    'type'      => 'text',
                    'label'     => 'Subject',
                ),
                'message'   => array(
                    'type'      => 'textarea',
                    'label'     => 'Message'
                ),
                'image' => array(
                    'type'      => 'file',
                    'mime'      => array('image/png','image/jpeg','image/gif'),
                    'size'      => array('max' => 100 * 1024),
                    'multiple'  => true,
                    'label'     => 'Attach image',
                ),
                'submit'    => array(
                    'type'      => 'submit',
                    'value'     => 'Отправить',
                ),
            ),
        ));
    }

}
