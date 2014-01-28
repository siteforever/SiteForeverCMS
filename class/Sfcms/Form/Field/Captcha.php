<?php
namespace Sfcms\Form\Field;

use App;
use Sfcms\Form\FormFieldAbstract;
use Sfcms\Request;

/**
 * Поле каптчи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Captcha extends FormFieldAbstract
{
    protected $type =   'captcha';
    protected $class =  '';
    protected $required = true;
    protected $datable = false;

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    protected function checkValue($value)
    {
        $captcha_code = $this->request->getSession()->get('captcha_code');
        $this->request->getSession()->remove('captcha_code');
        if (strtolower($captcha_code) == strtolower($value)) {
            return true;
        }
        $this->msg = 'Code is not valid';
        return false;
    }
}
