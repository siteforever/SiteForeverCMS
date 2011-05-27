<?php
/**
 * Класс, реализующий хлебные крошки
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
 
class View_Breadcrumbs
{

    private $_pieces    = array();

    /**
     * @var string
     */
    private $_separator = ' &rarr; ';

    public function __construct( array $pieces = array() )
    {
        $this->_pieces  = $pieces;
    }

    public function fromSerialize( $path )
    {
        $pathes     = @unserialize( $path );

        $this->_pieces  = array();
        if ( is_array( $pathes ) ) {
            foreach ( $pathes as $path ) {
                $this->_pieces[]    = $path;
            }
        }
    }

    public function fromJson( $path )
    {
        $pathes     = @json_decode( $path, true );

        $this->_pieces  = array();
        if ( is_array( $pathes ) ) {
            foreach ( $pathes as $path ) {
                $this->_pieces[]    = $path;
            }
        }
    }

    public  function toSerialize()
    {
        return serialize( $this->_pieces );
    }

    public function toJson()
    {
        return json_encode( $this->_pieces );
    }

    public function addPiece( $url, $name )
    {
        $this->_pieces[] = array( 'name'=>$name, 'url'=>$url );
    }

    public function clearPieces()
    {
        $this->_pieces  = array();
    }

    /**
     * @param  $name
     * @param  $url
     * @return View_Breadcrumbs_Crumb
     */
    protected function createCrumb( $name, $url )
    {
        return new View_Breadcrumbs_Crumb( $name, App::getInstance()->getRouter()->createLink( $url ) );
    }

    /**
     * @return string
     */
    public function render()
    {
        $pieces = array();
        if ( $this->_pieces ) {
            foreach ( $this->_pieces as $piece ) {
                $crumb  = $this->createCrumb( $piece['name'], $piece['url'] );
                $pieces[] = (string) $crumb;
            }
        }

        $result = join( $this->_separator, $pieces );

        return $result;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

}
