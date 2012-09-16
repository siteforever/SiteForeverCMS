<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

class Sfcms_Image_Scale_Priority extends Sfcms_Image_Scale_Abstr
{
    /**
     * Создаст отмасштабированное изображение
     * @param int $width
     * @param int $height
     * @param string $color
     * @return resource
     */
    public function getScalingImage( $width, $height, $color )
    {
        switch ( 'auto' ) {
            case $width:
                $k = $this->height / $height;
                $width = round( $this->width / $k );
                break;
            case $height:
                $k = $this->width / $width;
                $height = round( $this->height / $k );
                break;
            default:
                throw new Sfcms_Image_Exception('Height or width must be auto');
        }
        $thumb = imagecreatetruecolor( $width, $height );
        if( $this->image && $thumb ) {
            if( imagecopyresampled( $thumb, $this->image,
                0, 0, 0, 0,
                $width, $height,
                $this->width, $this->height )
            ) {
                return $thumb;
            }
        }
        throw new Sfcms_Image_Exception( 'Image not scalled' );
    }
}
