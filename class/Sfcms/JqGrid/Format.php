<?php
/**
 * Интерфейс форматировщиков данных для jqGrid
 */
namespace Sfcms\JqGrid;
interface Format
{
    /**
     * @param $params
     */
    public function __construct( $params );

    /**
     * @param $value
     * @return mixed
     */
    public function apply( $value );
}
