<?php
namespace Sfcms\Form\Field;

use App;
use Sfcms\Form\Field;

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
    protected function checkValue( $value )
    {
        // todo Надо внедрить в форму Request
        $captcha_code = App::getInstance()->getSession()->get('captcha_code');
        if (strtolower($captcha_code) == strtolower($value)) {
            return true;
        }
        $this->_msg = t('Code is not valid');
        $this->_form->addFeedback($this->_msg);
        return false;
    }

}
