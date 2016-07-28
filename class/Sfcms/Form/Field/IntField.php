<?php
/**
 * Поле целого числа
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class IntField extends Text
{
    protected $class   = 'int';
    protected $filter  = '/^-?\d*$/';
}
