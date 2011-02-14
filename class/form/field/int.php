<?php
/**
 * Поле целого числа 
 * @author keltanas
 */
class Form_Field_Int extends Form_Field_Text
{
	protected $type    = 'int';
	protected $class   = 'int';
	protected $filter  = '/^-?\d*$/';
}