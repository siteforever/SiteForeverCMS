<?php
/*
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File:     function.thumb.php
 * Type:     function
 * Name:     thumb
 * Purpose:  Выведет изображение предварительного просмотра
 * -------------------------------------------------------------
 *
 * $method: 1 - Add field, 2 - Crop
 *
 * @example {thumb src="/files/catalog/0001/trade.jpg" width="200"}
 *
 */
function smarty_function_thumb( $params )
{
    $src    = isset( $params[ 'src' ] ) ?  $params[ 'src' ] : null;
    $class  = isset( $params[ 'class' ] ) ? $params[ 'class' ] : '';
    $width  = isset( $params[ 'width' ] ) ? $params[ 'width' ] : 'auto';
    $height = isset( $params[ 'height' ] ) ? $params[ 'height' ] : 'auto';
    $method = isset( $params[ 'method' ] ) ? $params[ 'method' ] : 1;
    $color  = isset( $params[ 'color' ] ) ? $params[ 'color' ] : 'FFFFFF';

    if ( 'auto' == $width && 'auto' == $height ) {
        return 'You need to specify the width or height';
    }
    if ( 'auto' == $width || 'auto' == $height ) {
        $method = Sfcms_Image_Scale::METHOD_PRIORITY;
    }

    if( ! $src ) {
        $src = '/images/no-image.png';
    }

    $src = urldecode( str_replace(array('/','\\'),DIRECTORY_SEPARATOR,$src) );

    $alt    = isset( $params['alt'] ) ? $params['alt'] : $src;
    $path   = pathinfo( $src );
    $path[ 'thumb' ] = '/thumbs' . $path[ 'dirname' ] . '/' . '.thumb.' .$path[ 'filename' ]
                     . '-' . $width . 'x' . $height . '-'. $color . '-' . $method . '.' . $path[ 'extension' ];

    // @todo Может негативно сказаться на производительности. Подумать, как сделать иначе
    if ( ! is_dir( dirname( ROOT . $path['thumb'] ) ) ) {
        @mkdir( dirname( ROOT . $path['thumb'] ), 0775, true );
    } elseif ( ! is_writable( dirname( ROOT . $path['thumb'] ) ) ) {
        @chmod( dirname( ROOT . $path['thumb'] ), 775 );
    }

    if ( ! file_exists( ROOT . $path[ 'thumb' ] ) ) {
        $img             = new Sfcms_Image( ROOT . $src );
        $thumb           = $img->createThumb( $width, $height, $method, $color );
        $thumb->saveToFile( ROOT . $path[ 'thumb' ] );
    }

    return '<img width="'.$width.'" height="'.$height.'" alt="'.$alt.'" src="'
        . str_replace( array('/','\\'), '/' ,$path['thumb'] ).'"'
        . ($class ? ' class="'.$class.'"' : '').'>';
}