<?php
/**
 * Поле десятичного дробного числа 
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field\Text;

class Float extends Text
{
    protected $_class   = 'float';
    protected $_filter  = '/^-?\d*[\.|\,]?\d*$/';
}