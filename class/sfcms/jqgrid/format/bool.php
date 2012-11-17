<?php
/**
 * Визуализация двоичных данных
 *
 * Принимает 2 параметра
 *      yes - строка, которая должна отобразиться на 1
 *      no - строка, которая должна отобразиться на 0
 *
 * Например
 *  'format' => array(
 *       'bool' => array('yes'=>'ok','no'=>'fail')
 *   )
 */
namespace Sfcms\JqGrid\Format;

use Sfcms;
use Sfcms\JqGrid\Format;

class Bool implements Format
{
    private $params;

    /**
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function apply($value)
    {
        $yes = isset( $this->params['yes'] ) ? $this->params['yes'] : Sfcms::html()->icon('tick');
        $no  = isset( $this->params['no'] ) ? $this->params['no'] : Sfcms::html()->icon('cross');
//        return empty( $value )
//            ? Sfcms::html()->icon('lightbulb_off')
//            : Sfcms::html()->icon('lightbulb');
//        return empty( $value )
//            ? Sfcms::html()->icon('lock')
//            : Sfcms::html()->icon('lock_open');
//        return empty( $value )
//            ? Sfcms::html()->icon('female')
//            : Sfcms::html()->icon('male');
//        return empty( $value ) ? '' : Sfcms::html()->icon('new');
        return empty( $value ) ? $no : $yes;
    }

}
