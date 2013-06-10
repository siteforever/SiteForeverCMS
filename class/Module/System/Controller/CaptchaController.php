<?php
/**
 * Выводит каптчу
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\System\Controller;

use Sfcms\Controller;

class CaptchaController extends Controller
{
    /**
     * Init default config
     */
    public function init()
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


    /**
     * Index action
     * @return mixed
     */
    public function indexAction()
    {
        $h  = $this->config->get('captcha.height');
        $w  = $this->config->get('captcha.width');
        $l  = $this->config->get('captcha.length');

        $fontsize   = round( $h * 0.7, 2 );

        $img    = imagecreatetruecolor( $w, $h );

        imagefill( $img, 0, 0, $this->config->get('captcha.bgcolor') );

        $text = $this->generateString($l, '/[ABCEFGHIKMNOP]/');

        $this->request->getSession()->set('captcha_code', $text);

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
        return $img;
    }

    /**
     * @param $len
     * @param $pattern
     *
     * @return string
     */
    protected function generateString($len, $pattern)
    {
        $c   = $len;
        $str = '';
        while ($c > 0) {
            $charcode = rand(33, 122);
            $chr      = chr($charcode);
            if (preg_match($pattern, $chr)) {
                $str .= $chr;
                $c--;
            }
        }

        return $str;
    }
}
