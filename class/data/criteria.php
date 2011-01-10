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
        $sql[]  = "FROM {$this->table}";
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
        return join("\n", $sql);
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
