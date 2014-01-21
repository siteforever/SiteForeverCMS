<?php
/**
 * Поле целого числа
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Int extends Text
{
    protected $type    = 'number';
    protected $class   = 'int';
    protected $filter  = '/^-?\d*$/';
}
