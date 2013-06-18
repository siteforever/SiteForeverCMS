<?php
/**
 * Data provider for using with jqGrid
 */
namespace Sfcms\JqGrid;

use Sfcms;
use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Model;
use Sfcms\Db\Criteria;
use Pager;
use InvalidArgumentException;

use Sfcms\JqGrid\Format;
use Sfcms\Request;

class Provider
{
    /** @var Request */
    private $request = null;

    /** @var Model */
    private $model = null;

    /** @var Criteria */
    private $criteria = null;

    /** @var int */
    private $limit  = 10;

    /** @var array */
    private $fields = null;

    /** @var string */
    private $url = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Set model finder
     * @param Model $model
     */
    public function setModel( Model $model )
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
     * @param Criteria $criteria
     */
    public function setCriteria( Criteria $criteria )
    {
        $this->criteria = $criteria;
    }

    /**
     * @return Criteria|null
     * @throws \InvalidArgumentException
     */
    public function getCriteria()
    {
        if ( null === $this->criteria ) {
            throw new InvalidArgumentException('Criteria is not defined');
        }
        if ( $this->criteria->condition && $searchCond = $this->createSearchCondition() ) {
            $this->criteria->condition .= " AND {$searchCond}";
        }
//        $this->request->getLogger()->log($this->criteria->condition, 'condition');
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
        return $this->request->get('rows', FILTER_VALIDATE_INT, 10);
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        $sidx = $this->request->get('sidx');
        $sord = $this->request->get('sord');
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

        $controller = isset($params['controller'])
            ? $params['controller']
            : $this->request->getController();
        $action     = isset($params['action']) ? $params['action'] : 'grid';

        $config = array(
            'url' => Sfcms::html()->url($controller . '/' . $action),
            'datatype'   => "json",
            'colNames'   => array_map(function ($v) {
                if (is_array($v) && isset($v['title'])) {
                    return $v['title'];
                }
                if ( is_string( $v )) {
                    return $v;
                }
                return '';
            }, array_values($this->getFields())),
            'colModel'   => array_map(function ($k,$v) {
                $return = array(
                    'name' => $k,
                    'index' => $k,
                    'search' => false,
                );
                if ( is_array( $v ) ) {
                    if ( isset( $v['value'] ) ) {
                        $return['name'] = $v['value'];
                    }
                    if( isset($v['width']) ) {
                        $return['width'] = $v['width'];
                    }
                    if( isset($v['sortable']) ) {
                        $return['sortable'] = $v['sortable'];
                    }
                    if ( ! empty($v['search']) ) {
                        $return['search'] = true;
                        if ( is_array( $v['search'] ) ) {
                            $return['stype'] = 'select';
                            if ( isset($v['search']['value']) && is_array($v['search']['value']) ) {
                                $return['editoptions']['value'] = implode(';',$v['search']['value']);
                            }
                        }
                    }
                }
                return $return;
            }, array_keys($this->getFields()), array_values($this->getFields())),
//            'autoWidth' => true,
            'autoHeight' => true,
            'height'    => isset( $params['height'] ) ? $params['height'] : $rowNum * $cellHeight,
            'rowNum'    => $rowNum,
            'rowList'   => isset( $params['rowList']) ? explode(',',$params['rowList']) : array(10, 20, 30),
            'pager'     => sprintf('#%s_pager',$name),
            'sortname'  => isset( $params['sortname'] ) ? $params['sortname'] : 'id',
            'viewrecords' => true,
            'sortorder' => "desc",
            'multiselect' => isset( $params['multiselect'] ) ? $params['multiselect'] : false,
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
        $pager = new Sfcms\Pager($count, $this->getPerpage(), '', $this->request);

        $criteria->limit = $pager->limit;
        $criteria->order = $this->getOrder();

        $fields = $this->getFields();
        $with = array_filter(array_map(function($field){
            if (is_array($field)) {
                if (isset($field['value']) && strpos($field['value'],'.')) {
                    list($return) = explode('.', $field['value']);
                    return $return;
                }
                if (isset($field['with'])) {
                    return $field['with'];
                }
            }
            return false;
        }, $fields));

//        $this->app->getLogger()->log( $with ? '1' : '0','$with' );

        /** @var $collection Collection */
        $collection = $this->getModel()->with($with)->findAll( $criteria );

        $result = array();
        $result['page'] = $pager->page;
        $result['total'] = $pager->pages;
        $result['records'] = $pager->count;

        $_ = $this;
        /** @var $obj Object */
        $result['rows'] = array_map(function(Object $obj) use ($_, $fields) {
            return array(
                'id' => $obj->id,
                'cell' => array_map(function($key,$val) use ($obj, $_) {
                    if (is_array($val)) {
                        if (isset($val['value'])) {
                            $key = $val['value'];
                        }
                    }
                    if (false !== strpos($key, '.', 1)) {
                        $p = explode('.', $key);
                        /** @var $subObj Object */
                        $subObj = $obj->get($p[0]);
                        $value = $subObj ? $subObj->get($p[1]) : 'null';
                    } else {
                        $value = $obj->get($key);
                    }
                    if (isset($val['format']) && is_array($val['format'])) {
                        array_walk( $val['format'], function ($val, $key) use ($obj, $_, &$value) {
                            /** @var $format Format */
                            $format = $_->getFormat( $key, $val );
                            $value = $format->apply( $value, $obj );
                        });
                    }
                    return $value;
                }, array_keys($fields), array_values($fields)),
            );
        }, iterator_to_array($collection));

        return json_encode($result);
    }

    /**
     * Формирует условие для поиска
     * @return string
     */
    protected function createSearchCondition()
    {
        $result = array();

        $searchField  = $this->request->get( 'searchField' );
        $searchOper   = $this->request->get( 'searchOper' );
        $searchString = $this->request->get( 'searchString' );

        $operations = array(
            'eq'    => "`:field` = ':value' ",
            'ne'    => "`:field` <> ':value' ",
            'lt'    => "`:field` < ':value' ",
            'le'    => "`:field` <= ':value' ",
            'gt'    => "`:field` > ':value' ",
            'ge'    => "`:field` >= ':value' ",
            'bw'    => "`:field` LIKE ':value%' ",
            'bn'    => "`:field` NOT LIKE ':value%' ",// не начинается с
            'in'    => "`:field` LIKE '%:value%' ", // находится в
            'ni'    => "`:field` NOT LIKE '%:value%' ", // не находится в
            'ew'    => "`:field` LIKE '%:value' ", // Заканчивается на
            'en'    => "`:field` NOT LIKE '%:value' ", //  Не заканчивается на
            'cn'    => "`:field` LIKE '%:value%' ", //  содержит
            'nc'    => "`:field` NOT LIKE '%:value%' ", // не содержит
        );

        if ( $searchField && $searchOper && $searchString ) {
            $result[] = str_replace(array(':field', ':value'), array($searchField, $searchString), $operations[$searchOper]);
        }

        $request = $this->request;

        $fields = $this->getFields();

        $result += array_filter( array_map(function($id) use ($request, $fields, $operations) {
            $field = $fields[$id];
            if ( empty( $field['search'] ) ) {
                return false;
            }
            $sopt = 'bw';
            if ( isset( $field['search']['sopt'] ) ) {
                $sopt = $field['search']['sopt'];
            }
            $val = $request->get($id);
            return $val
                ? str_replace(array(':field',':value'),array($id, $val),$operations[$sopt])
                : false;
        }, array_keys($fields)));

        return implode(' AND ', $result);
    }

    /**
     * @param $name
     * @param $params
     * @return Format
     */
    public function getFormat( $name, $params ) {
        $className = 'Sfcms\\JqGrid\\Format\\'.ucfirst( strtolower($name) );
        return new $className( $params );
    }

}
