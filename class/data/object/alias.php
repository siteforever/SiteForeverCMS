<?php
/**
 * Объект алиаса
 * @author keltanas <nikolay@gmail.com>
 * @link http://siteforever.ru
 *
 * @property $id
 * @property $alias
 * @property $url
 */

class Data_Object_Alias extends Data_Object
{
    /**
     * @param string|array $params
     * @return void
     */
    public function setParams( $params = array() )
    {
        if ( is_array( $params ) ) {
            $params = serialize( $params );
        }
        if ( ! is_string( $params ) ) {
            throw new Exception('invalid params');
        }
        $this->data[ 'params' ]  = $params;
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
        return $this->getModel()->generateAlias( $string );
    }

    /**
     * @param string $model
     * @return Model_Alias
     */
    public function getModel( $model = '' )
    {
        return parent::getModel( $model );
    }

    /**
     * @param array $data
     * @param string $separator
     * @return string
     */
    public function generateAliasFromArray( $data, $separator = '/' )
    {
        return $this->getModel()->generateAliasFromArray( $data, $separator );
    }
}
