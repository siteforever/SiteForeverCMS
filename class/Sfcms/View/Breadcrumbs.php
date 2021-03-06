<?php
/**
 * Класс, реализующий хлебные крошки
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\View;

class Breadcrumbs
{

    private $_pieces    = array();

    /**
     * @var string
     */
    private $_separator = '';

    public function __construct( array $pieces = array() )
    {
        $this->fromArray( $pieces );
    }

    /**
     * @param array $path
     * @return Breadcrumbs
     */
    public function fromArray( array $path )
    {
        $this->_pieces  = $path;
        return $this;
    }

    /**
     * @param $path
     * @return Breadcrumbs
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
     * @return Breadcrumbs
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
     * @return Breadcrumbs
     */
    public function addPiece( $url, $name )
    {
        $this->_pieces[] = array( 'name'=>$name, 'url'=>$url );
        return $this;
    }

    /**
     * @return Breadcrumbs
     */
    public function clearPieces()
    {
        $this->_pieces  = array();
        return $this;
    }

    /**
     * @param  $name
     * @param  $url
     * @return Breadcrumbs\Crumb
     */
    protected function createCrumb($name, $url)
    {
        return new Breadcrumbs\Crumb($name, $url, $this->_separator);
    }

    /**
     * @return string
     */
    public function render()
    {
        $pieces = array();
        if ( $this->_pieces ) {
            $countDown = count($this->_pieces);
            foreach ( $this->_pieces as $piece ) {
                $countDown--;
                $crumb  = $this->createCrumb($piece['name'], $countDown ? $piece['url'] : null);
                $pieces[] = (string) $crumb;
            }
        }

        $result = '<ul class="breadcrumb"><li>' . join( $this->getSeparator() . '</li><li>', $pieces ).'</li></ul>';

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
