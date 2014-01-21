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
            'fields'    => array(
                'name'      => array(
                    'type'      => 'text',
                    'label'     => 'Ваше имя',
                    'required',
                ),
                'email'     => array(
                    'type'      => 'text',
                    'label'     => 'Email',
                    'required',
                ),
                'title'     => array(
                    'type'      => 'text',
                    'label'     => 'Тема',
                ),
                'message'   => array(
                    'type'      => 'textarea',
                    'label'     => 'Сообщение'
                ),
                'submit'    => array(
                    'type'      => 'submit',
                    'value'     => 'Отправить',
                ),
            ),
        ));
    }

}
