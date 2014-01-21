<?php
/**
 * Поле отправки данных
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Submit extends FormFieldAbstract
{
    protected $type = 'submit';
    protected $datable = false;
}
