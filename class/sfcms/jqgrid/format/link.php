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
            if ( ':' == $val{0} ) {
                return $obj->get( substr($val,1) );
            }
            return $val;
        },$params);

        return Sfcms::html()->link( $value, null, $params );
    }
}
