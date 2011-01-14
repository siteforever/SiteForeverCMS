<?php
/**
 * Модель маршрутов
 */
class model_Routes extends Model
{
    function onCreateTable()
    {
        $this->db->insert($this->table, array(
             'pos'      => '0',
             'alias'    => 'rss',
             'controller'=>'rss',
             'action'   => 'index',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '1',
             'alias'    => 'admin/edit.*',
             'controller'=>'admin',
             'action'   => 'edit',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '2',
             'alias'    => 'admin/add.*',
             'controller'=>'admin',
             'action'   => 'add',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '3',
             'alias'    => 'admin/users.*',
             'controller'=>'users',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '7',
             'alias'    => 'admin/settings',
             'controller'=>'settings',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '8',
             'alias'    => 'admin/routes',
             'controller'=>'routes',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '9',
             'alias'    => 'elfinder',
             'controller'=>'elfinder',
             'action'   => 'index',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '10',
             'alias'    => 'admin/order',
             'controller'=>'order',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '11',
             'alias'    => 'admin/catalog',
             'controller'=>'catalog',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '12',
             'alias'    => 'admin/news',
             'controller'=>'news',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '13',
             'alias'    => 'admin',
             'controller'=>'admin',
             'action'   => 'index',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '14',
             'alias'    => 'users/logout',
             'controller'=>'users',
             'action'   => 'logout',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '15',
             'alias'    => 'users/edit',
             'controller'=>'users',
             'action'   => 'edit',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '16',
             'alias'    => 'users/restore',
             'controller'=>'users',
             'action'   => 'restore',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '17',
             'alias'    => 'users/register',
             'controller'=>'users',
             'action'   => 'register',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '18',
             'alias'    => 'users/login',
             'controller'=>'users',
             'action'   => 'login',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '19',
             'alias'    => 'users',
             'controller'=>'users',
             'action'   => 'index',
             'active'   => '1',
             'system'   => '0',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '20',
             'alias'    => 'templates/edit',
             'controller'=>'templates',
             'action'   => 'edit',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '21',
             'alias'    => 'templates',
             'controller'=>'templates',
             'action'   => 'index',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '22',
             'alias'    => 'system',
             'controller'=>'system',
             'action'   => 'index',
             'active'   => '1',
             'system'   => '1',
        ));
        $this->db->insert($this->table, array(
             'pos'      => '23',
             'alias'    => 'admin/gallery',
             'controller'=>'gallery',
             'action'   => 'admin',
             'active'   => '1',
             'system'   => '1',
        ));
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_Routes';
    }

    public function objectClass()
    {
        return 'Data_Object_Route';
    }
}