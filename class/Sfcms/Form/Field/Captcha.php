<?php
namespace Sfcms\Form\Field;

use App;
use Sfcms\Form\Field;
use Sfcms\Request;

/**
 * Поле каптчи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Captcha extends Field
{
    protected $_type =   'text';
    protected $_class =  'input-small';
    protected $_required = true;

    public function htmlInput( $field )
    {
        $field['value']    = 'value=""';
        $field['class']    = array( 'captcha' );
        return parent::htmlInput( $field )
            . '<img src="/?controller=captcha" alt="captcha" />'
            . '<span class="captcha-reload">Обновить</span>';
    }

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
        $this->_msg = $this->t('Code is not valid');
        return false;
    }
}
