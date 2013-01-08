<?php
/**
 * Форма обратной связи по умолчанию
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 *
 * @property Form_Field $name
 * @property Form_Field $email
 * @property Form_Field $title
 * @property Form_Field $message
 */
class Forms_Feedback_Default extends Form_Form
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
