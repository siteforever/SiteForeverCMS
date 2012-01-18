<?php
/**
 * Интерфейс масштабера
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */


abstract class Sfcms_Image_Scale_Abstr
{

    protected $image;
    protected $width;
    protected $height;

    protected $k;

    /**
     * @param resource $image
     * @param string   $method
     */
    function __construct( $image )
    {
        $this->image  = $image;
        $this->width  = imagesx( $this->image );
        $this->height = imagesy( $this->image );
    }


    /**
     * Создаст отмасштабированное изображение
     * @param int $width
     * @param int $height
     * @param string $color
     * @return resource
     */
    abstract function getScalingImage( $width, $height, $color );

    /**
     * Преобразует цвет из формата ffee99 в формат для GD2
     * @param resource $image
     * @param string $hex_color
     * @return int
     */
    protected function getColorFromHex( $image, $hex_color )
    {
        if( $hex_color == '-1' ) {
            $bgcolor = imagecolorat( $this->image, 0, 0 );
        } elseif( strlen( $hex_color ) == 6 ) {
            $hred    = hexdec( substr( $hex_color, 0, 2 ) );
            $hgr     = hexdec( substr( $hex_color, 2, 2 ) );
            $hblue   = hexdec( substr( $hex_color, 4, 2 ) );
            $bgcolor = imagecolorallocate( $image, $hred, $hgr, $hblue );
        } else {
            $bgcolor = null;
        }
        return $bgcolor;
    }


    /**
     * @return float
     */
    function scalledHeight()
    {
        return round( $this->height / $this->k );
    }

    /**
     * @return float
     */
    function scalledWidth()
    {
        return round( $this->width / $this->k );
    }


}
