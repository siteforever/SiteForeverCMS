<?php
/**
 * Data provider for using with jqGrid
 */
namespace Sfcms\JqGrid;

use App;
use Sfcms_Model;
use Db_Criteria;
use Data_Object;
use Data_Collection;
use Pager;
use InvalidArgumentException;

class Provider
{
    /** @var App */
    private $app = null;

    /** @var Sfcms_Model */
    private $model = null;

    /** @var Db_Criteria */
    private $criteria = null;

    /** @var int */
    private $limit  = 10;

    /** @var array */
    private $fields = null;

    /** @var string */
    private $url = null;

    public function __construct( App $app )
    {
        $this->app = $app;
    }

    /**
     * Set model finder
     * @param Sfcms_Model $model
     */
    public function setModel( Sfcms_Model $model )
    {
        $this->model = $model;
    }

    public function getModel()
    {
        if ( null === $this->model ) {
            throw new InvalidArgumentException('Model is not defined');
        }
        return $this->model;
    }

    /**
     * Set database criteria for searching
     * @param \Db_Criteria $criteria
     */
    public function setCriteria( Db_Criteria $criteria )
    {
        $this->criteria = $criteria;
    }

    /**
     * @return \Db_Criteria|null
     * @throws \InvalidArgumentException
     */
    public function getCriteria()
    {
        if ( null === $this->criteria ) {
            throw new InvalidArgumentException('Criteria is not defined');
        }
        return $this->criteria;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getUrl()
    {
        if ( null === $this->url ) {
            throw new InvalidArgumentException('Url is not defined');
        }
        return $this->url;
    }



    /**
     * Установить поля для вывода
     * @param array $fields
     */
    public function setFields( array $fields )
    {
        $this->fields = $fields;
    }

    /**
     * Вернет список полей
     *
     * индексы:  поля для запроса в базу данных (выбор, сортировка)
     * значения: строка - Наименование поля
     *           массив - Список значений для вывода:
     *              value - Поле (комбинированное поле) для вывода значения, может отличатся для объекта от структуры БД
     *              title - Название поля, которое будет выведено в заголовке
     *              width - Ширина колонки
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getFields()
    {
        if ( null === $this->fields ) {
            throw new InvalidArgumentException('Fields list is not defined');
        }
        return $this->fields;
    }

    /**
     * @return int
     */
    public function getPerpage()
    {
        return $this->app->getRequest()->get('rows', FILTER_VALIDATE_INT, 10);
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        $sidx = $this->app->getRequest()->get('sidx');
        $sord = $this->app->getRequest()->get('sord');
        if ( $sidx && $sord ) {
            return $sidx . ' ' . $sord;
        }
        return '';
    }

    /**
     * Вернет JS конфиг для активации
     * @param string $name
     * @param array $params
     * @return array
     */
    public function getConfig( $name, array $params = array() )
    {
        $rowNum = isset( $params['rowNum'] ) ? $params['rowNum'] : 20;
        $cellHeight = 28; // todo костыль, определяющий высоту таблицы, исходя из высоты ячейки

        $config = array(
            'url'=>'/goods/jqgrid',
            'datatype'   => "json",
            'colNames'   => array_map(function ($v) {
                if (is_array($v) && isset($v['title'])) {
                    return $v['title'];
                }
                return $v;
            }, array_values($this->getFields())),
            'colModel'   => array_map(function ($k,$v) {
                $return = array(
                    'name' => $k,
                    'index' => $k,
                );
                if ( is_array( $v ) ) {
                    if ( isset( $v['value'] ) ) {
                        $return['name'] = $v['value'];
                    }
                    if( isset($v['width']) ) {
                        $return['width'] = $v['width'];
                    }
                }
                return $return;
            }, array_keys($this->getFields()), array_values($this->getFields())),
            'height'    => isset( $params['height'] ) ? $params['height'] : $rowNum * $cellHeight,
            'rowNum'    => $rowNum,
            'rowList'   => isset( $params['rowList']) ? explode(',',$params['rowList']) : array(10, 20, 30),
            'pager'     => sprintf('#%s_pager',$name),
            'sortname'  => isset( $params['sortname'] ) ? $params['sortname'] : 'id',
            'viewrecords' => true,
            'sortorder' => "desc",
        );
        return $config;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getJsonData()
    {
        $criteria = $this->getCriteria();
        $count  = $this->getModel()->count( $criteria );
        $pager  = new Pager( $count, $this->getPerpage() );

        $criteria->limit = $pager->limit;
        $criteria->order = $this->getOrder();

        $with = array_filter(array_map(function($field){
            if ( is_array($field) && isset($field['value']) && strpos($field['value'],'.') ) {
                list( $return ) = explode('.', $field['value']);
                return $return;
            }
            return false;
        },$this->getFields()));

        $this->app->getLogger()->log( $with,'$with' );

        /** @var $collection Data_Collection */
        $collection = $this->getModel()->with($with)->findAll( $criteria );

        $result = array();
        $result['page'] = $pager->page;
        $result['total'] = $pager->pages;
        $result['records'] = $pager->count;

        $_ = $this;
        /** @var $obj Data_Object */
        $result['rows'] = array_map(function( $obj ) use ( $_ ) {
            return array(
                'id' => $obj->getId(),
                'cell' => array_map(function($key,$val) use ( $obj ) {
                    if ( is_array( $val ) ) {
                        if ( isset($val['value']) ) {
                            $key = $val['value'];
                        }
                    }
                    if ( strpos($key,'.') ) {
                        $p = explode('.', $key);
                        $subObj = $obj->get($p[0]);
                        return $subObj ? $subObj->get($p[1]) : 'fail';
                    } else {
                        return $obj->get( $key );
                    }
                },array_keys( $_->getFields() ), array_values($_->getFields()) ),
            );
        }, iterator_to_array( $collection ) );

        return json_encode($result);
    }
}
