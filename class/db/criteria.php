<?php
/**
 * Контейнер критериев для запроса
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Db_Criteria
{
    public  $select = '*';

    public  $from   = '';

    public  $condition  = '';

    public  $params = array();

    public  $order  = '';

    public  $group  = '';

    public  $having = '';

    public  $limit  = '';

    public  function __construct( $criteria = array() )
    {
        if ( isset( $criteria['cond'] ) ) {
            $this->condition    = $criteria['cond'];
            unset( $criteria['cond'] );
        }

        if ( isset( $criteria['where'] ) ) {
            $this->condition    = $criteria['where'];
            unset( $criteria['where'] );
        }

        foreach( $criteria as $key => $val ) {
            $this->$key = $val;
        }
    }
}
