<?php
/**
 * Поле десятичного дробного числа
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field\Text;

class FloatField extends Text
{
    protected $class   = 'float';
    protected $filter  = '/^-?\d*[\.|\,]?\d*$/';
}
