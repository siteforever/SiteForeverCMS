<?php
/**
 * Изображение
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
class Sfcms_Image
{
    protected $img = null;


    public function __construct( $img = null )
    {
        if( is_string( $img ) && file_exists( $img ) ) {
            $this->img = self::loadFromFile( $img );
        } else {
            $this->img = $img;
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
    public function createThumb( $width, $height, $method, $color = '-1' )
    {
        $scale = $this->getScale( $method );
        $thumb = $scale->getScalingImage( $width, $height, $color );
        if( $thumb ) {
            return new Sfcms_Image( $thumb );
        }
    }

    public function getScale( $method )
    {
        switch ( $method ) {
            case '1':
            case Sfcms_Image_Scale::METHOD_ADD:
                $scale = new Sfcms_Image_Scale( Sfcms_Image_Scale::METHOD_ADD, $this->img );
                break;
            case Sfcms_Image_Scale::METHOD_PRIORITY:
                $scale = new Sfcms_Image_Scale( Sfcms_Image_Scale::METHOD_PRIORITY, $this->img );
                break;
            case '2':
            case Sfcms_Image_Scale::METHOD_CROP:
            default:
                $scale = new Sfcms_Image_Scale( Sfcms_Image_Scale::METHOD_CROP, $this->img );
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
        return Sfcms_Image_Loader::load( $filename );
    }

    /**
     * Сохранить в файл
     * @param  $filename
     * @return void
     */
    public function saveToFile( $filename )
    {
        if ( ! is_writable( dirname( $filename ) ) ) {
            throw new Sfcms_Image_Exception( 'Directory "'.dirname( $filename ).'" is not writable' );
        }
        Sfcms_Image_Loader::save( $this->img, $filename );
    }

}
