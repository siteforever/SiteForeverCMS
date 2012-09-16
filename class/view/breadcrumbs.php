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
    private $_separator = '<span class="divider">&gt;</span>';

    public function __construct( array $pieces = array() )
    {
        $this->fromArray( $pieces );
    }

    /**
     * @param array $path
     * @return View_Breadcrumbs
     */
    public function fromArray( array $path )
    {
        $this->_pieces  = $path;
        return $this;
    }

    /**
     * @param $path
     * @return View_Breadcrumbs
     */
    public function fromSerialize( $path )
    {
        $pathes     = @unserialize( $path );

        $this->_pieces  = array();
        if ( is_array( $pathes ) ) {
            foreach ( $pathes as $path ) {
                $this->_pieces[]    = $path;
            }
        }
        return $this;
    }

    /**
     * @param $path
     * @return View_Breadcrumbs
     */
    public function fromJson( $path )
    {
        $pathes     = @json_decode( $path, true );

        $this->_pieces  = array();
        if ( is_array( $pathes ) ) {
            foreach ( $pathes as $path ) {
                $this->_pieces[]    = $path;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public  function toSerialize()
    {
        return serialize( $this->_pieces );
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode( $this->_pieces );
    }

    /**
     * @param $url
     * @param $name
     * @return View_Breadcrumbs
     */
    public function addPiece( $url, $name )
    {
        $this->_pieces[] = array( 'name'=>$name, 'url'=>$url );
        return $this;
    }

    /**
     * @return View_Breadcrumbs
     */
    public function clearPieces()
    {
        $this->_pieces  = array();
        return $this;
    }

    /**
     * @param  $name
     * @param  $url
     * @return View_Breadcrumbs_Crumb
     */
    protected function createCrumb( $name, $url )
    {
        return new View_Breadcrumbs_Crumb( $name, $url, $this->_separator );
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

        $result = '<ul class="breadcrumb"><li>'.join( $this->getSeparator() . '</li><li>', $pieces ).'</li></ul>';

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
