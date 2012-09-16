<?php
/**
 * Масштаб изображения
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
class Sfcms_Image_Scale
{
    /**
     * @var Image_Scale_Abstract
     */
    private $_scaller;

    const   METHOD_CROP = 'crop';
    const   METHOD_ADD  = 'add';
    const   METHOD_PRIORITY = 'priority';

    /**
     * @throws Image_Scale_Exception
     * @param string $method
     * @param resource $image
     */
    function __construct( $method, $image )
    {
        switch ( $method ) {
            case self::METHOD_CROP:
                $this->_scaller = new Sfcms_Image_Scale_Crop( $image );
                break;
            case self::METHOD_ADD:
                $this->_scaller = new Sfcms_Image_Scale_Add( $image );
                break;
            case self::METHOD_PRIORITY:
                $this->_scaller = new Sfcms_Image_Scale_Priority( $image );
                break;
            default:
                throw new Sfcms_Image_Scale_Exception( 'Undefined scalling method' );
        }
    }

    /**
     * Создаст отмасштабированное изображение
     * @param  $width
     * @param  $height
     * @param  $color
     * @return Image
     */
    function getScalingImage( $width, $height, $color = '-1' )
    {
        return $this->_scaller->getScalingImage( $width, $height, $color );
    }
}
