<?php
class model_Settings extends Model
{
    
    /**
     * Поиск по ID
     * @param $id
     */
    function find( $id )
    {
        $this->data = $this->db->fetch("SELECT * FROM ".DBSETTINGS." WHERE id = '{$id}' LIMIT 1");
        return $this->data;
    }
    
    /**
     * Поиск всего
     * @param $cond
     * @param $order
     */
    function findAll( $cond = '' )
    {
        $where = '';
        if ( $cond ) {
            $where = " WHERE {$cond} ";
        }
        $data_all = $this->db->fetchAll("SELECT * FROM ".DBSETTINGS." $where");
        return $data_all;
    }
    
    
    
}