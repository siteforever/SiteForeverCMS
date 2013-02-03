<?php
/**
 * Форматирует значение в изображение
 */
namespace Sfcms\JqGrid\Format;

use Sfcms;
use Sfcms\JqGrid\Format;

class Timestamp implements Format
{
    protected $params;

    public function __construct( $params )
    {
        $this->params = $params;
    }

    public function apply( $value )
    {
        return strftime( $this->params['format'], $value );
    }
}
