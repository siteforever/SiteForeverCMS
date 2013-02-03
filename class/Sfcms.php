<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

use Sfcms\Html;
use Sfcms\i18n;
 
class Sfcms
{
    static private $instance;

    static protected $html = null;

    private function __construct() {}

    /**
     * @static
     * @return Sfcms
     */
    static function getInstance()
    {
        if ( null === self::$instance ) {
            self::$instance = new Sfcms();
        }
    }

    /**
     * HTML Helper
     * @static
     * @return Html
     */
    static function html()
    {
        if ( null === self::$html ) {
            self::$html = new Html();
        }
        return self::$html;
    }

    /**
     * @return i18n
     */
    static function i18n()
    {
        return i18n::getInstance();
    }

    /**
     * @param string $message
     * @param string $label
     */
    static function log( $message, $label = '' )
    {
        App::getInstance()->getLogger()->log( $message, $label );
    }

    /**
     * @param $_source_path
     * @param string $_file
     * @return bool|resource
     */
    static function watermark($_source_path, $_file=null)
    {
        /* Проверка - подключена ли библиотека GD - если её нет, вам необходимо либо самому подключить эту библиотеку
        * (В файле php.ini, секция extensions, необходимо прописать либо раскомментировать строку:
        *   extension=php_gd2.dll - в windows. */
        if (!extension_loaded('gd')) {
            throw new RuntimeException('GD library not found on this server');
        }

        $_watermark_path = ROOT . '/watermark.png';

        if ( ! file_exists( $_watermark_path ) ) {
            return false;
        }

        // Загрузка штампа и фото, для которого применяется водяной знак (называется штамп или печать)
        $stamp  = Sfcms_Image_Loader::load($_watermark_path);
        $im     = Sfcms_Image_Loader::load($_source_path);

        // Установка полей для штампа и получение высоты/ширины штампа
        $sw = $cw = imagesx($stamp);
        $sh = $ch = imagesy($stamp);
        $iw = imagesx($im);
        $ih = imagesy($im);

        if ( $sw > $iw ) {
            $cw = $iw;
            $k  = $sw / $iw;
            $ch = $sh / $k;
        }

        $cy = abs( $ih / 2 - $ch / 2 );
        $cx = abs( $iw / 2 - $cw / 2 );

        // Копирование изображения штампа на фотографию с помощью смещения края
        // и ширины фотографии для расчета позиционирования штампа.
        imagecopyresampled(
            $im, $stamp,
            $cx, $cy, 0, 0,
            $cw, $ch, $sw, $sh
        );

        if ( null !== $_file ) {
            Sfcms_Image_Loader::save($im, $_file);
        }
        return $im;
    }

}
