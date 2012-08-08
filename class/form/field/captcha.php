<?php
/**
 * Поле каптчи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Form_Field_Captcha extends Form_Field
{
    protected $_type =   'captcha';

    public function htmlInput( $field )
    {
        $field['value']    = 'value=""';
        $field['class'][]  = 'captcha';
        return parent::htmlInput( $field )
            . '<img src="/?controller=captcha" alt="captcha" />'
            . '<span class="siteforever_captcha_reload">Обновить</span>';
    }

    public function validate()
    {
        $classes    = explode( ' ', trim($this->_class) );
        foreach ( $classes as $i => $class ) {
            if ( $class == 'error' ) {
                unset( $classes[ $i ] );
            }
        }

        if ( strtolower( $_SESSION['captcha_code'] ) == strtolower( $this->getValue() ) ) {
            $this->_error   = 0;
        }
        else {
            $this->_error   = 1;
            $this->_error_string    = 'Код не верный';
        }

        if ( $this->_error > 0 ) {
            $this->_form->addFeedback( $this->_error_string );
            $classes[] = 'error';
            $this->_class    = join(' ', $classes);
        }

        return ! $this->_error;
    }
}
