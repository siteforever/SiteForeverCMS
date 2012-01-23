<?php
/**
 * Выводит каптчу
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Controller_Captcha extends Sfcms_Controller
{
    function init()
    {
        $this->config->setDefault('captcha', array(
            'width'     => 100,
            'height'    => 25,
            'color'     => 0x000000,
            'bgcolor'   => 0xffffff,
            'font'      => SF_PATH.'/misc/captcha/infroman.ttf',
            'length'    => 6,
         ));
    }

    function indexAction()
    {
        $h  = $this->config->get('captcha.height');
        $w  = $this->config->get('captcha.width');
        $l  = $this->config->get('captcha.length');

        $fontsize   = round( $h * 0.7, 2 );

        $img    = imagecreatetruecolor( $w, $h );

        imagefill( $img, 0, 0, $this->config->get('captcha.bgcolor') );

        $text   = $this->app()->getAuth()->generateString( $l, '/[ABCEFGHIKMNOP]/' );

        $_SESSION['captcha_code']   = $text;

        $step   = round( ( $w * 0.8 ) / $l );
        $halfstep   = round( $step / 2 );
        $quartstep  = round( $step / 4 );

        for ( $i = 0; $i < $l; $i++ ) {
            imagettftext( $img, $fontsize, rand(-15, 15),
                          $i * $step + rand(-$quartstep, $quartstep) + $halfstep, rand($fontsize-2, $fontsize+2),
                          $this->config->get('captcha.color'),
                          $this->config->get('captcha.font'),
                          $text{$i} );
        }

        header('Content-type: image/png');
        imagepng( $img );
        imagedestroy( $img );
        die();
    }
}
