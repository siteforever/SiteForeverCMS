<?php
/**
 * Выводит каптчу
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\System\Controller;

use function imagecreatetruecolor;
use Sfcms\Controller;

class CaptchaController extends Controller
{
    /**
     * Index action
     * @return mixed
     */
    public function indexAction()
    {
        $config = $this->container->getParameter('captcha');
        $h  = $config['height'];
        $w  = $config['width'];
        $l  = $config['length'];

        $fontsize = round($h * 0.7, 2);

        $img = imagecreatetruecolor($w, $h);

        imagefill($img, 0, 0, $config['bgcolor']);

        if ($this->request->getSession()->has('captcha_code') && !$this->request->query->has('hash')) {
            $text = $this->request->getSession()->get('captcha_code');
        } else {
            $text = $this->generateString($l, '/[abcdefhkmnopt23456789]/');
            $this->request->getSession()->set('captcha_code', $text);
        }

        $step = round(($w * 0.8) / $l);
        $halfstep = round($step / 2);
        $quartstep = round($step / 4);

        for ($i = 0; $i < $l; $i++) {
            imagettftext(
                $img,
                $fontsize,
                rand(-10, 10),
                $i * $step + rand(-$quartstep, $quartstep) + $halfstep,
                rand($fontsize - 2, $fontsize + 2),
                $config['color'],
                $config['font'],
                $text{$i}
            );
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
