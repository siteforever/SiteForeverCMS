<?php
/**
 * Модель Алиаса
 * @author keltanas <nikolay@gmail.com>
 * @link http://siteforever.ru
 */

class Model_Alias extends Sfcms_Model
{
    private $_aliases_cache = array();

    private $_url_cache = array();

    /**
     * @param $path
     * @return Data_Object_Alias
     */
    public function findByAlias( $path )
    {
        if( isset( $this->_aliases_cache[ $path ] ) ) {
            return $this->_aliases_cache[ $path ];
        }
        $result                        = $this->find(
            array(
                'cond'      => ' `alias` = ? ',
                'params'    => array( $path ),
            )
        );
        $this->_aliases_cache[ $path ] = $result;
        return $result;
    }

    /**
     * @param $path
     * @return Data_Object
     */
    public function findByUrl( $path )
    {
        if( isset( $this->_url_cache[ $path ] ) ) {
            return $this->_url_cache[ $path ];
        }
        $result                    = $this->find(
            array(
                'cond'      => ' `url` = ? ',
                'params'    => array( $path ),
            )
        );
        $this->_url_cache[ $path ] = $result;
        return $result;
    }

    /**
     * @param $string
     * @return mixed|string
     */
    public function generateAlias( $string )
    {
        $string = mb_strtolower( trim( $string ) );
        $string = Sfcms::i18n()->translit( $string );
        $string = preg_replace( '@[^a-z0-9]+@', '_', $string );
        $string = trim( $string, '_' );
        return $string;
    }

    /**
     * @param array $data
     * @param string $separator
     * @return string
     */
    public function generateAliasFromArray( $data, $separator = '/' )
    {
        foreach( $data as $k => $v ) {
            $data[ $k ] = $this->generateAlias( $v );
        }

        return implode( $separator, $data );
    }
}
