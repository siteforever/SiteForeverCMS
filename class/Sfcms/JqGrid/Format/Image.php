<?php
/**
 * Форматирует значение в изображение
 */
namespace Sfcms\JqGrid\Format;

use Sfcms;
use Sfcms\JqGrid\Format;

class Image implements Format
{
    protected $params;

    public function __construct( $params )
    {
        $this->params = $params;
    }

    public function apply( $value )
    {
        $params = $this->params;
        $params['src'] = $value;
        return Sfcms::html()->thumb( $params );
    }
}
