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
 * @example {thumb src="/files/catalog/0001/trade.jpg" width="200"}
 *
 */
function smarty_function_thumb( $params )
{
    $src    = $params[ 'src' ];
    $width  = $params[ 'width' ];
    $height = $params[ 'height' ];
    $method = $params[ 'method' ];
    $color  = $params[ 'color' ];

    if( ! $src ) {
        return 'Parametr SRC not found';
    }

    $img = new Sfcms_Image( $src );

    $thumb = $img->createThumb( $width, $height, $method, $color );

    $path = pathinfo( $src );
    $thumb->saveToFile( $path[ 'dirname' ] . DIRECTORY_SEPARATOR
                        . $path[ 'filename' ] . '_' . $width . '_' . $height . $path[ 'extension' ] );

    $app  = App::getInstance();
    $tpl  = $app->getTpl();
    $user = $app->getAuth()->currentUser();

    $tpl->assign( 'form', Model::getModel( 'User' )->getLoginForm() );
    $tpl->assign( 'user', $user->getAttributes() );
    if( $user->perm == USER_GUEST ) {
        $tpl->assign( 'auth', '1' );
    }
    else {
        $tpl->assign( 'auth', '0' );
    }

    return $tpl->fetch( 'users.head_login' );
    ;
}