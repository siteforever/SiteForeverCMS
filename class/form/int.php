<?php
/**
 * Поле целого числа 
 * @author keltanas
 */
class form_Int extends form_Text
{
	protected $type    = 'int';
	protected $class   = 'int';
	protected $filter  = '/^-?\d*$/';
}