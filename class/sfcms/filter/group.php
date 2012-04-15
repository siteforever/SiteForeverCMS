<?php
/**
 * Группа фильтров
 * @author: keltanas <keltanas@gmail.com>
 */
abstract class Sfcms_Filter_Group
{
    /**
     * @var int
     */
    protected $_n;
    /**
     * @var string
     */
    protected $_name;
    /**
     * @var string|array|object
     */
    protected $_data = array();

    /**
     * @param $n
     * @param $name
     * @param $data
     */
    public function __construct( $n, $name, $data )
    {
        $this->_n    = $n;
        $this->_name = $name;
        $this->_data = $data;
    }

    /**
     * @param array|object|string $data
     */
    public function setData( $data )
    {
        $this->_data = $data;
    }

    /**
     * @return array|object|string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Заполнить данные
     * @param int $id
     * @param Sfcms_Model $model
     */
    public function fillData( $id, Sfcms_Model $model )
    {
    }

    /**
     * @param int $n
     */
    public function setN( $n )
    {
        $this->_n = $n;
    }

    /**
     * @return int
     */
    public function getN()
    {
        return $this->_n;
    }

    /**
     * @param string $name
     */
    public function setName( $name )
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


}