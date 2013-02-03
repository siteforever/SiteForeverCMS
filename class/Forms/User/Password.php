<?php
/**
 * Форма изменения пароля
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Forms\User;

use Sfcms\Form\Form;

class Password extends Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'password',
            'fields'    => array(
                'password'  => array('type'=>'password', 'label'=>'Старый пароль', 'required', 'autocomplete'=>'off'),
                'password1' => array('type'=>'password', 'label'=>'Новый пароль',  'required', 'autocomplete'=>'off'),
                'password2' => array('type'=>'password', 'label'=>'Повтор пароля', 'required', 'autocomplete'=>'off'),
                'submit'    => array('type'=>'submit', 'value'=>'Изменить'),
            ),
        ));
    }
}
