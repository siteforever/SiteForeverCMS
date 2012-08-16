<?php
/**
 * Построитель запросов на основе критериев
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Criteria
{
    /**
     * @var Db_Criteria
     */
    private $_criteria;
    
    /**
     * @var string
     */
    private $_table;

    /**
     * @param string $table
     * @param array|Db_Criteria $criteria
     * @throws Data_Exception
     */
    public function __construct( $table, $criteria = null )
    {
        if ( is_array( $criteria ) ) {
            $this->_criteria = new Db_Criteria( $criteria );
        } elseif ( is_null( $criteria ) ) {
            $this->_criteria = new Db_Criteria();
        } elseif ( is_object( $criteria ) && $criteria instanceof Db_Criteria ) {
            $this->_criteria = $criteria;
        } else {
            throw new Data_Exception('Criteria format fail');
        }
        $this->_table    = $table;
    }

    /**
     * Создает SQL строку по критерию
     * @return string
     */
    public function getSQL()
    {
        $sql    = array();
        $sql[]  = "SELECT {$this->_criteria->select}";
        $sql[]  = "FROM `{$this->_table}`";
        if ( $this->_criteria->condition ) {
            $sql[]  = "WHERE {$this->_criteria->condition}";
        }
        else {
            $this->_criteria->params = array();
        }
        if ( $this->_criteria->order ) {
            $sql[]  = "ORDER BY {$this->_criteria->order}";
        }

        if ( $this->_criteria->group ) {
            $sql[]  = "GROUP BY {$this->_criteria->group}";
        }

        if ( $this->_criteria->having ) {
            $sql[]  = "HAVING {$this->_criteria->having}";
        }

        if ( $this->_criteria->limit ) {
            $sql[]  = "LIMIT {$this->_criteria->limit}";
        }

        $str_sql = join(' ', $sql);

        if ( count($this->_criteria->params ) ) {
            $q_start    = 0;
            foreach ( $this->_criteria->params as $key => $val ) {
                if ( is_array( $val ) ) {
                    $val    = implode("','",$val); // Внешние апострофы добавяться в след. условии
                }

                if ( ! is_numeric( $val ) ) {
                    if ( is_string( $val ) ) {
                        $val = trim( $val, "'" );
                        $val = "'{$val}'";
                    } else {
                        continue;
                    }
                }

                if ( is_numeric( $key ) ) {
                    $q_start    = strpos( $str_sql, '?', $q_start );
                    if ( false !== $q_start ) {
                        $str_sql = substr_replace( $str_sql, $val, $q_start++, 1 );
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
