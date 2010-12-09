<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 30.09.2010
 * Time: 23:56:10
 * To change this template use File | Settings | File Templates.
 */

class model_gallery extends Model
{

    protected $table;

    /**
     * @var form_Form
     */
    protected $form;


    function createTables()
    {
        $this->table    = DBPREFIX.'gallery';

        if ( ! $this->isExistTable( $this->table ) ) {
            $this->db->query("
                CREATE TABLE `{$this->table}` (
                  `id` int(11) NOT NULL auto_increment,
                  `category_id` int(11),
                  `name` varchar(250) default NULL,
                  `link` varchar(250) DEFAULT NULL,
                  `description` text,
                  `image` varchar(250),
                  `middle` varchar(250),
                  `thumb` varchar(250),
                  `pos` int(11) NOT NULL default '0',
                  `main` tinyint(4) NOT NULL default '0',
                  `hidden` tinyint(4) NOT NULL default '0',
                  PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8
            ");
        }

        $this->setData(array(
            'name'      => '',
            'category_id'   => '0',
            'image'     => '',
            'middle'    => '',
            'thumb'     => '',
        ));
    }


    function find( $id )
    {
        $this->data = $this->db->fetch("SELECT * FROM {$this->table} WHERE id = {$id} LIMIT 1");
        return $this->data;
    }

    function getNextPosition( $category_id )
    {
        return $this->db->fetchOne("SELECT MAX(pos)+1 FROM {$this->table} WHERE category_id = {$category_id}");
    }

    /**
     * @param array $cond = array()
     * @param string $limit = ''
     * @return array
     *
     * $model = findAll(array('category_id = 10', 'hidden = 0'), '20, 10');
     */
    function findAll($cond = array(), $limit = '')
    {
        $where = '';
        if ( count($cond) ) {
            $where = ' WHERE '.implode(' AND ', $cond);
        }
        if ( $limit != '' ) {
            $limit = ' LIMIT '.$limit;
        }
        return $this->db->fetchAll("SELECT * FROM {$this->table} {$where} ORDER BY `pos` {$limit}");
    }

    /**
     * Количество по условию
     * @param array $cond
     * @return string
     */
    function getCount($cond = array())
    {
        $where = '';
        if ( count($cond) ) {
            $where = ' WHERE '.implode(' AND ', $cond);
        }
        return $this->db->fetchOne("SELECT COUNT(*) FROM {$this->table} {$where}");
    }

    function insert()
    {
        unset( $this->data['id'] );
        $id = $this->db->insert($this->table, $this->data);
        $this->set('id', $id);
        return $id;
    }

    function update()
    {
        return $this->db->update($this->table, $this->data, "id = {$this->data['id']}");
    }

    /**
     * Удалить изображения и запись из базы
     * @param int $id
     * @return void
     */
    function delete( $id = null )
    {
        if ( is_null( $id ) ) {
            $id = $this->getId();
        }

        $data = $this->find( $id );
        if ( $data ) {
            if ( $data['thumb'] && file_exists(ROOT.$data['thumb']) ) {
                @unlink ( ROOT.$data['thumb'] );
            }
            if ( $data['middle'] && file_exists(ROOT.$data['middle']) ) {
                @unlink ( ROOT.$data['middle'] );
            }
            if ( $data['image'] && file_exists(ROOT.$data['image']) ) {
                @unlink ( ROOT.$data['image'] );
            }
            return $this->db->delete( $this->table, " id = {$id} " );
        }
        return false;
    }

    function reposition()
    {
        $positions = $this->request->get('positions');
        $new_pos = array();
        foreach ( $positions as $pos => $id ) {
            $new_pos[] = array('id'=>$id, 'pos'=>$pos);
        }
        return $this->db->insertUpdateMulti($this->table, $new_pos);
    }

    /**
     * Переключение активности изображения
     * @param int $id
     * @return bool|int
     */
    function hideSwitch( $id )
    {
        if( ! $this->find( $id ) ) {
            return false;
        }
        if ( $this->data['hidden'] ) {
            $this->data['hidden'] = '0';
        }
        else {
            $this->data['hidden'] = '1';
        }
        if ( ! $this->update() ) {
            return false;
        }
        if ( $this->data['hidden'] ) {
            return 1;
        }
        else {
            return 2;
        }
    }
}
