<?php
/**
 * Форма обратной связи по умолчанию
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 *
 * @property Field $name
 * @property Field $email
 * @property Field $title
 * @property Field $message
 */
use Sfcms\Form\Form;
use Sfcms\Form\Field;

class Forms_Feedback_Personal extends Form
{
    public function __construct()
    {
        return parent::__construct( array(
            'name'      => 'feedbackPersonal',
            'class'     => 'ajax-validate form-horizontal',
            'fields'    => array(
                'captcha'   => array(
                    'type'      => 'captcha',
                    'label'     => 'Введите код',
                    'required',
                ),
                'to'    => array(
                    'type'  => 'text',
                    'label' => 'Кому адресовано обращение?',
                    'required',
                ),
                'name'      => array(
                    'type'      => 'text',
                    'label'     => 'Фамилия, Имя, Отчество гражданина',
                    'required',
                ),
                'email'     => array(
                    'type'      => 'text',
                    'label'     => 'Адрес эл. почты',
                    'filter'    => 'email',
                    'required',
                ),
                'address'     => array(
                    'type'      => 'textarea',
                    'label'     => 'Почтовый адрес',
                    'required',
                ),
                'replyto'     => array(
                    'type'      => 'radio',
                    'label'     => 'Куда направить ответ?',
                    'value'     => '0',
                    'variants'  => array(
                        '1' => 'На адрес эл. почты', '2' => 'На почтовый адрес',
                    ),
                    'required',
                ),
                'phone'     => array(
                    'type'      => 'text',
                    'label'     => 'Контактный телефон',
                    'filter'    => 'phone',
                    'help-inline'    => '+7 xxx xxx-xx-xx',
                    'required',
                ),
                'message'   => array(
                    'type'      => 'textarea',
                    'label'     => 'Текст сообщения',
                    'required',
                ),
            ),
        ));
    }

}
