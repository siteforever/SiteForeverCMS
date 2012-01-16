<?php
/**
 * Изображение
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
namespace sfcms;

class Image
{
    protected $img  = null;


    function __construct( $img = null )
    {
        if ( is_string( $img ) && file_exists( $img ) ) {
            $this->img    = self::loadFromFile( $img );
        }
        else {
            $this->img  = $img;
        }
    }

    /**
     * Создать миниатюру
     * @param int $width
     * @param int $height
     * @param int $method
     * @param string $color
     * @return
     */
    function createThumb( $width, $height, $method, $color = '-1' )
    {
        $scale  = $this->getScale( $method );
        $thumb  = $scale->getScalingImage( $width, $height, $color );
        if ( $thumb ) {
            return new Image( $thumb );
        }
    }

    function getScale( $method )
    {
        switch ( $method ) {
            case '1':
                $scale = new Image\Scale(Image\Scale::METHOD_ADD, $this->img);
                break;
            case '2':
            default:
                $scale = new Image\Scale(Image\Scale::METHOD_CROP, $this->img);
        }
        return $scale;
    }

    /**
     * Загрузить из файла
     * @static
     * @param  $filename
     * @return resource
     */
    static protected function loadFromFile( $filename )
    {
        return Image\Loader::load( $filename );
    }

    /**
     * Сохранить в файл
     * @param  $filename
     * @return void
     */
    function saveToFile( $filename )
    {
        Image\Loader::save( $this->img, $filename );
    }

}
