<?php
/**
 * Масштабер с обрезанием полей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Image_Scale_Crop extends Image_Scale_Abstract
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

        $k = min( array( $kw, $kh ) );


        // вычисляем размеры реальной миниатюры
        $th = round( $this->height / $k );
        $tw = round( $this->width / $k );

        if ( $th == $height ) {
            $from_y = 0;
            $from_x = - round( abs( $tw - $width ) / 2 );
        } elseif ( $tw == $width ) {
            $from_x = 0;
            $from_y = - round( abs( $th - $height ) / 2 );
        }

        $thumb  = imagecreatetruecolor( $width, $height );

        if ($this->image && $thumb ) {
            if ( imagecopyresampled ( $thumb, $this->image,
                $from_x, $from_y, 0, 0,
                $tw, $th, $this->width, $this->height
            ) ) {
                return $thumb;
            }
        }
        throw new Image_Scale_Exception('Image not scalled');
    }
}
