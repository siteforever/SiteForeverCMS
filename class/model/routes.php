<?php
/**
 * Модель маршрутов
 */
class model_Routes extends Model
{

    function createTables()
    {
        if ( ! $this->isExistTable(DBROUTES) ) {
            $this->db->query("
                CREATE TABLE `".DBROUTES."` (
                  `id` int(11) NOT NULL auto_increment,
                  `pos` int(11) NOT NULL default '0',
                  `alias` varchar(200) NOT NULL default '',
                  `controller` varchar(50) NOT NULL default 'index',
                  `action` varchar(50) NOT NULL default 'index',
                  `active` tinyint(4) NOT NULL default '1' COMMENT 'Вкл/выкл',
                  `protected` tinyint(4) NOT NULL default '0' COMMENT 'Для зарегистрированных',
                  `system` tinyint(4) NOT NULL default '0' COMMENT 'Для администраторов',
                  PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8
            ");
            $this->db->insert(DBROUTES, array(
                 'pos'      => '1',
                 'alias'    => 'admin',
                 'controller'=>'admin',
                 'action'   => 'index',
                 'system'   => '1',
            ));
            $this->db->insert(DBROUTES, array(
                 'pos'      => '2',
                 'alias'    => 'admin/edit',
                 'controller'=>'admin',
                 'action'   => 'edit',
                 'system'   => '1',
            ));
            $this->db->insert(DBROUTES, array(
                 'pos'      => '4',
                 'alias'    => 'admin/routes',
                 'controller'=>'routes',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert(DBROUTES, array(
                 'pos'      => '6',
                 'alias'    => 'users/login',
                 'controller'=>'users',
                 'action'   => 'login',
                 'system'   => '0',
            ));
        }
    }

	/**
	 * Поиск по ID
	 * @param $id
	 */
    function find( $id )
    {
        $this->data = $this->db->fetch("SELECT * FROM ".DBROUTES." WHERE id = '{$id}' LIMIT 1");
        return $this->data;
    }

    /**
     * Поиск всех маршрутов
     * Здесь можно подключить кэширование
     * и не обращаться к БД лишний раз
     * @param $cond
     * @param $order
     */
    function findAll( $cond = '' )
    {
        $where = '';
    	if ( $cond ) {
    		$where = " WHERE {$cond} ";
    	}
    	$data_all = $this->db->fetchAll("SELECT * FROM ".DBROUTES." $where ORDER BY pos");
    	return $data_all;
    }



}