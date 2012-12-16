<?php
/**
 * Форматирует значение как ссылку
 */
namespace Sfcms\JqGrid\Format;

use Sfcms;
use Sfcms\JqGrid\Format;
use InvalidArgumentException;

use Data_Object;

class Link implements Format
{
    protected $params;

    /**
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @param $value
     * @return string
     * @throws InvalidArgumentException
     */
    public function apply($value)
    {
        $params = $this->params;
        /** @var $obj Data_Object */
        $obj = func_get_arg(1);

        $params = array_map(function($val) use( $obj ) {
            return preg_match_all('/(:\w+)/',$val, $m)
                ? array_reduce( $m[1], function( $val, $next ) use ( $obj ) {
                        return str_replace( $next, $obj->get( substr($next,1) ), $val );
                    }, $val)
                : $val;
        },$params);

        return Sfcms::html()->link( $value, null, $params );
    }
}
