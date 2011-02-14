<?php
/**
 * Построитель запросов на основе критериев
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Criteria
{
    private $criteria;
    private $table;

    /**
     * @param string $table
     * @param array $criteria
     */
    function __construct( $table, $criteria = array() )
    {
        if ( isset( $criteria['params'] ) && ! is_array( $criteria['params'] ) ) {
            throw new Data_Criteria_Exception('Criteria params must be Array');
        }

        $this->criteria = array_merge(
            array(
                'select'    => '*',
                'cond'      => '',
                'params'    => array(),
                'limit'     => '',
            ),
            $criteria
        );
        $this->table    = $table;
    }

    /**
     * Создает SQL строку по критерию
     * @return string
     */
    function getSQL()
    {
        $sql    = array();
        $sql[]  = "SELECT {$this->criteria['select']}";
        $sql[]  = "FROM `{$this->table}`";
        if ( $this->criteria['cond'] ) {
            $sql[]  = "WHERE {$this->criteria['cond']}";
        }
        else {
            $this->criteria['params'] = array();
        }
        if ( ! empty( $this->criteria['order'] ) ) {
            $sql[]  = "ORDER BY {$this->criteria['order']}";
        }
        if ( $this->criteria['limit'] ) {
            $sql[]  = "LIMIT {$this->criteria['limit']}";
        }

        $str_sql = join(' ', $sql);
        if ( isset($this->criteria['params']) && is_array($this->criteria['params']) ) {
            $q_start    = 0;
            foreach ( $this->criteria['params'] as $par => $val ) {

                if ( is_array( $val ) ) {
                    $val    = implode("','",$val); // Внешние апострофы добавяться в след. условии
                }

                if ( ! is_numeric( $val ) )
                    if ( is_string( $val ) )
                        $val    = "'{$val}'";
                    else
                        continue;
                
                if ( is_numeric( $par ) ) {
                    $q_start    = strpos( $str_sql, '?', $q_start );
                    $str_sql = substr_replace( $str_sql, $val, $q_start, 1 );
                    $q_start++;
                }
                else {
                    $str_sql = str_replace($par, $val, $str_sql);
                }
            }
        }

        return preg_replace('/\s+/', ' ', trim($str_sql) );
    }

    /**
     * Вернет параметры для запроса
     * @return array
     */
    function getParams()
    {
        return $this->criteria['params'];
    }
}
