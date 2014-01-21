<?php
/**
 * Текстовое поле пароля
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Password extends FormFieldAbstract
{
    protected $type      = 'password';

    public function htmlInput($field)
    {
        unset($field['value']);
        return parent::htmlInput($field);
    }


}
