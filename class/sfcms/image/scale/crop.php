<?php
/**
 * Масштабер с обрезанием полей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Sfcms_Image_Scale_Crop extends Sfcms_Image_Scale_Abstr
{
    /**
     * Создаст отмасштабированное изображение
     * @param $width
     * @param $height
     * @param $color
     * @return \resource
     * @throws Exception
     */
    public function getScalingImage( $width, $height, $color )
    {
        // 1. пропорции
        $kh = $this->height / $height;
        $kw = $this->width / $width;

        $this->k = min( array( $kw, $kh ) );

        $from_x = 0;
        $from_y = 0;

        if( $this->scalledHeight() == $height ) {
            $from_x = - round( abs( $this->scalledWidth() - $width ) / 2 );
        } elseif( $this->scalledWidth() == $width ) {
            $from_y = - round( abs( $this->scalledHeight() - $height ) / 2 );
        }

        $thumb = imagecreatetruecolor( $width, $height );

        if( $this->image && $thumb ) {
            if( imagecopyresampled( $thumb, $this->image,
                $from_x, $from_y, 0, 0,
                $this->scalledWidth(), $this->scalledHeight(),
                $this->width, $this->height
            )
            ) {
                return $thumb;
            }
        }
        throw new Exception( 'Image not scalled' );
    }

}
