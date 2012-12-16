<?php
/**
 * Default route regulations
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Route;
use Sfcms\Route;

class Defaults extends Route
{
    /**                             S
     * @param $route
     *
     * @return mixed
     */
    public function route( $route )
    {
        $routePieces = explode( '/', $route );

        if ( count( $routePieces ) == 1 ) {
            return array(
                'controller' => $routePieces[ 0 ],
                'action'     => 'index',
            );
        } elseif( count( $routePieces ) > 1 ) {
            return array(
                'controller' => $routePieces[ 0 ],
                'action'     => $routePieces[ 1 ],
                'params'     => $this->extractAsParams( array_slice( $routePieces, 2 ) ),
            );
        }
        return false;
    }

}
