<?php
/**
 * Поле каптчи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Form_Field_Captcha extends Form_Field
{
    protected $type =   'captcha';

    function htmlTpl($html)
    {
        $html   = preg_replace('/value=[\"\\\'].*?[\"\\\']/i', '', $html);
        $html   = preg_replace('/class=[\"\\\'](.*?)[\"\\\']/i', 'class="$1 captcha"', $html);
        $html   = preg_replace('/\s+/', ' ', $html);
        //print htmlspecialchars( $html );
        $html   .=  '<img src="/?controller=captcha" alt="captcha" />';
        $html   .=  '<span class="siteforever_captcha_reload">Обновить</span>';

        return parent::htmlTpl($html);
    }

    function validate()
    {
        $classes    = explode( ' ', trim($this->class) );
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
            $this->form->addFeedback( $this->_error_string );
            $classes[] = 'error';
            $this->class    = join(' ', $classes);
        }

        return ! $this->_error;
    }
}
