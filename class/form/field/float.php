<?php
/**
 * Поле десятичного дробного числа 
 * @author keltanas
 */
class Form_Field_Float extends Form_Field_Text
{
    protected $_class   = 'float';
    protected $_filter  = '/^-?\d*[\.|\,]?\d*$/';

}