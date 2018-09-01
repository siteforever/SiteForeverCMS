<?php
/**
 * Загрузчик изображений
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Sfcms_Image_Loader
{

    /**
     * Загрузит изображение
     * @param string $filename
     * @return resource
     * @throws Sfcms_Image_Exception
     */
    static function load( $filename )
    {
        if ( file_exists( $filename ) ) {
            list( $width, $height, $type, $attr ) = getimagesize( $filename );
            switch ( $type ) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg( $filename );
                    break;

                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif( $filename );
                    break;

                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng( $filename );
                    break;
                default:
                    throw new Sfcms_Image_Exception( 'Undefined type' );
            }

            return $image;
        }
        throw new Sfcms_Image_Exception( 'Image file not found' );
    }

    /**
     * Сохранит изображение в файл
     * @static
     * @param resource $img
     * @param string $filename
     * @return bool
     */
    static function save( $img, $filename )
    {
        $ret = false;
        if( preg_match( '/.*\.([^.]+)$/', $filename, $match ) ) {
            switch (strtolower( $match[ 1 ] )) {
                case 'png':
                    $ret = imagepng( $img, $filename );
                    break;
                case 'gif':
                    $ret = imagegif( $img, $filename );
                    break;
                default: // Jpeg by default
                    $ret = imagejpeg( $img, $filename, 90 );
            }
        }
        return $ret;
    }

}
