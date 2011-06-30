<?php
/**
 * Объект алиаса
 * @author keltanas <nikolay@gmail.com>
 * @link http://siteforever.ru
 */

class Data_Object_Alias extends Data_Object
{
    /**
     * @param array $params
     * @return void
     */
    public function setParams( $params = array() )
    {
        $this->__set( 'params', serialize( $params ) );
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return unserialize( $this->__get( 'params' ) );
    }

    /**
     * @param $string
     * @return mixed|string
     */
    public function generateAlias( $string )
    {
        $string = mb_strtolower( trim($string) );
        $string = translit( $string );
        $string = preg_replace('@[^a-z0-9]+@', '_', $string);
        $string = trim($string, '_');
        return $string;
    }

    /**
     * @param array $data
     * @param string $separator
     * @return string
     */
    public function generateAliasFromArray( $data, $separator = '/' )
    {
        foreach ( $data as $k => $v ) {
            $data[$k]   = $this->generateAlias( $v );
        }

        return $separator.implode( $separator, $data );
    }
}
