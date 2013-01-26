<?php
/**
 * Поле целого числа 
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field;

class Int extends Text
{
    protected $_type    = 'number';
    protected $_class   = 'int';
    protected $_filter  = '/^-?\d*$/';
}