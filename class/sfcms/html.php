<?php
/**
 * Хэлперы HTML
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms;

use App;
use Sfcms_Image;
use Sfcms_Image_Exception;
use Sfcms_Image_Scale;

class Html
{
    static private $counter = 0;

    /**
     * Позволяет создать только 1 экземпляр
     * @throws Exception
     */
    public function __construct() {
        self::$counter ++;
        if ( self::$counter > 1 ) {
            throw new Exception('HTML class singleton');
        }
    }

    /**
     * Вернет строку URL для указанных параметров
     * @param string|null $url
     * @param array $params
     * @return string
     */
    public function url( $url, $params = array() )
    {
        return App::getInstance()->getRouter()->createLink( $url, $params );
    }

    /**
     * Вернет HTML код для иконки
     * @param $name
     * @param string $title
     * @return string
     */
    public function icon( $name, $title = '' )
    {
        return sprintf('<img title="%1$s" alt="%1$s" src="/images/admin/icons/%2$s.png">', $title?$title:$name, $name);
    }

    /**
     * Содаст HTML ссылку
     * @param  $text
     * @param  $url
     * @param array $params
     * @return string
     */
    public function link( $text, $url = "#", $params = array(), $class = "" )
    {
        $attributes = array();
        if ( $class ) {
            $params['class'] = $class;
        }
        if ( isset( $params['nofollow'] )) {
            if ( $params['nofollow'] ) {
                $attributes[ ] = 'rel="nofollow"';
            }
            unset( $params['rel'], $params['nofollow']);
        }
        $attributes = array_merge(
            $attributes,
            $this->makeAttributes( $params, array('class','title','rel') )
        );

        if ( isset( $params['controller'] ) && '#' == $url ) {
            $url = null;
        }
        $attributes[] = $this->href( $url, $params );
        return sprintf('<a %s>%s</a>', trim(implode(' ', $attributes)), $text);
    }

    /**
     * Make attributes list by params list and pass attributes list
     * @param array $params
     * @param array $passKeys
     * @return array
     */
    protected function makeAttributes( &$params, $passKeys = array() )
    {
        $attributes = array_filter( array_map(function($key) use (&$params) {
            return isset( $params[$key] ) ? sprintf('%s="%s"', $key, $params[$key]) : false;
        },$passKeys) );

        $params = array_diff_key( $params, array_flip($passKeys) );

        foreach ( $params as $key => $val ) {
            switch ( substr( $key, 0, 4 ) ) {
                case 'html':
                    unset( $params[$key] ); // чистит регистрозависимые ключи вида htmlTarget
                    $key = strtolower( substr( $key, 4 ) );
                case 'data':
                    unset( $params[$key] ); // чистит ключи, относящиеся только к data: data-id
                    $attributes[] = sprintf('%s="%s"', $key ,$val);
            }
        }
        return $attributes;
    }

    /**
     * Создаст ссылку
     * @param string $url
     * @param array  $params
     * @return string
     */
    public function href( $url = '', $params = array() )
    {
        return sprintf('href="%s"', $this->url( $url, $params ));
    }

    /**
     * Вернет html-код для встевки уменьшеного изображения
     *
     * $method: 1 - Add field, 2 - Crop
     *
     * @param $params
     * @return string
     */
    public function thumb( $params )
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
            try {
                $img   = new Sfcms_Image( ROOT . $src );
                $thumb = $img->createThumb( $width, $height, $method, $color );
            } catch ( Sfcms_Image_Exception $e ) {
                return $e->getMessage();
            }
            $thumb->saveToFile( ROOT . $path[ 'thumb' ] );
        }
        if ( ! empty( $thumb ) ) {
            $sizes = "width=\"{$thumb->getWidth()}\" height=\"{$thumb->getHeight()}\"";
        } else {
            list( , , ,$sizes) = getimagesize( ROOT . $path['thumb'] );
        }

        return '<img '.$sizes.' alt="'.$alt.'" src="'
            . str_replace( array('/','\\'), '/' ,$path['thumb'] ).'"'
            . ($class ? ' class="'.$class.'"' : '').'>';
    }
}
