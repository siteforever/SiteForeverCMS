<?php
/**
 * Поле десятичного дробного числа 
 * @author keltanas
 */
class Form_Field_Float extends Form_Field_Text
{
    protected $type    = 'float';
    protected $class   = 'float';
    protected $filter  = '/^-?\d*[\.|\,]?\d*$/';

}