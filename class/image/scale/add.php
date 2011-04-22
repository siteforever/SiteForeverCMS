<?php
/**
 * Масштабер с добавлением полей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Image_Scale_Add extends Image_Scale_Abstract
{

    /**
     * Создаст отмасштабированное изображение
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param string $color
     * @return resource
     */
    function getScalingImage($width, $height, $color)
    {
        // 1. пропорции
        $kh = $this->height / $height;
        $kw = $this->width / $width;

        $this->k = max( array( $kw, $kh ) );

        if ( $this->scalledHeight() == $height ) {
            $from_y = 0;
            $from_x = round( abs( $this->scalledWidth() - $width ) / 2 );
        } elseif ( $this->scalledWidth() == $width ) {
            $from_x = 0;
            $from_y = round( abs( $this->scalledHeight() - $height ) / 2 );
        }

        $thumb  = imagecreatetruecolor( $width, $height );

        $bgcolor    = $this->getColorFromHex( $thumb, $color );
        if ( $bgcolor ) {
            imagefill( $thumb, 0, 0, $bgcolor ); // заливаем
        }

        if ($this->image && $thumb ) {
            if ( imagecopyresampled ( $thumb, $this->image,
                $from_x, $from_y, 0, 0,
                $this->scalledWidth(), $this->scalledHeight(),
                $this->width, $this->height
            ) ) {
                return $thumb;
            }
        }
        throw new Image_Scale_Exception('Image not scalled');
    }
}
