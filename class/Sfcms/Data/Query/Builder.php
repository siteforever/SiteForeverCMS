<?php
/**
 * Построитель запросов на основе критериев
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Query;

use Sfcms\Data\AbstractField;
use Sfcms\Data\Exception;
use Sfcms\Data\Field;
use Sfcms\Db\Criteria;
use Sfcms\Model;

class Builder
{
    /**
     * @var Criteria
     */
    private $_criteria;

    /**
     * @var string
     */
    private $_model;

    /**
     * @param Model $model
     * @param array|Criteria $criteria
     * @throws Exception
     */
    public function __construct(Model $model, $criteria = null)
    {
        if (is_array($criteria)) {
            $this->_criteria = new Criteria($criteria);
        } elseif (is_null($criteria)) {
            $this->_criteria = new Criteria();
        } elseif (is_object($criteria) && $criteria instanceof Criteria) {
            $this->_criteria = $criteria;
        } else {
            throw new Exception('Criteria format fail');
        }
        $this->_model= $model;
        $this->_criteria->from = $model->getTable();
    }

    /**
     * Создает SQL строку по критерию
     * @return string
     */
    public function getSQL()
    {
        $sql       = array();
        $select    = '*';
        $obj_class = $this->_model->objectClass();
        $fields    = call_user_func(array($obj_class, 'fields'));

        // Заменяем * на список полей
        if ( '*' == $this->_criteria->select ) {
            $this->_criteria->select = array_map(function(AbstractField $field){
                return $field->getName();
            },$fields);
        }

        if (is_string($this->_criteria->select)) {
            $select = $this->_criteria->select;
        } elseif (is_array($this->_criteria->select)) {
            $select = '`' . join('`,`', $this->_criteria->select) . '`';
        }
        $sql[]  = "SELECT {$select}";
        $sql[]  = "FROM `{$this->_criteria->from}`";
        if ($this->_criteria->condition) {
            $sql[] = "WHERE {$this->_criteria->condition}";
        } else {
            $this->_criteria->params = array();
        }
        if ($this->_criteria->order) {
            $sql[] = "ORDER BY {$this->_criteria->order}";
        }

        if ($this->_criteria->group) {
            $sql[] = "GROUP BY {$this->_criteria->group}";
        }

        if ($this->_criteria->having) {
            $sql[] = "HAVING {$this->_criteria->having}";
        }

        if ($this->_criteria->limit) {
            $sql[] = "LIMIT {$this->_criteria->limit}";
        }

        $str_sql = join(' ', $sql);

        if (count($this->_criteria->params)) {
            $q_start = 0;
            foreach ($this->_criteria->params as $key => $val) {
                if (is_array($val)) {
                    $values = array_filter(
                        array_map(
                            function ($v) {
                                return is_numeric($v) ? $v : ($v ? "'{$v}'" : false);
                            },
                            $val
                        ),
                        function ($v) {
                            return false !== $v;
                        }
                    );

                    if (0 == count($values)) {
                        throw new \InvalidArgumentException('Empty array');
                    }
                    sort($values);
                    $val = implode(',', $values); // Внешние апострофы добавяться в след. условии
                } elseif (is_string($val)) {
//                    $val = filter_var(
//                        trim($val, "'"),
//                        FILTER_SANITIZE_FULL_SPECIAL_CHARS,
//                        FILTER_FLAG_NO_ENCODE_QUOTES
//                    );
                    $val = preg_replace('/\\\'/', '\\\\\'', $val);
                    $val = "'{$val}'";
                } elseif (is_numeric($val)) {
                } else {
                    continue;
                }

                if (is_numeric($key)) {
                    $q_start = strpos($str_sql, '?', $q_start);
                    if (false !== $q_start) {
                        $str_sql = substr_replace($str_sql, $val, $q_start++, 1);
                    }
                } else {
                    $str_sql = str_replace($key, $val, $str_sql);
                }
            }
        }

        return preg_replace('/\s+/', ' ', trim($str_sql) );
    }

    /**
     * Вернет параметры для запроса
     * @return array
     */
    public function getParams()
    {
        return $this->_criteria->params;
    }
}
