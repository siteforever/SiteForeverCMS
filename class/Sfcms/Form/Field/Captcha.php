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

    public function validate()
    {
//        parent::validate();
//        $classes    = explode( ' ', trim($this->_class) );
//        foreach ( $classes as $i => $class ) {
//            if ( $class == 'error' ) {
//                unset( $classes[ $i ] );
//            }
//        }

        $captcha_code = App::getInstance()->getSession()->get('captcha_code');
        if ( strtolower( $captcha_code ) == strtolower( $this->getValue() ) ) {
            $this->_error &= 0;
        } else {
            $this->_error &= 1;
            $this->_msg    = 'Код не верный';
        }

        if ( $this->_error > 0 ) {
            $this->_form->addFeedback( $this->_msg );
            $classes[] = 'error';
            $this->_class    = join(' ', $classes);
        }

        return ! $this->_error;
    }
}
