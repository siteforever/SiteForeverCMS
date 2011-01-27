<?php
/**
 * Поле десятичного дробного числа 
 * @author keltanas
 */
class form_Float extends form_Text
{
    protected $type    = 'float';
    protected $class   = 'float';
    protected $filter  = '/^-?\d*[\.|\,]?\d*$/';

}