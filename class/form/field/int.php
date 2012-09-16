<?php
/**
 * Поле целого числа 
 * @author keltanas
 */
class Form_Field_Int extends Form_Field_Text
{
    protected $_type    = 'number';
    protected $_class   = 'int';
    protected $_filter  = '/^-?\d*$/';
}