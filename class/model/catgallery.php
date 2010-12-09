<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class model_CatGallery extends Model
{
    // Массив, проиднексированный по продуктам
    protected $data_all = array();
    protected $data = array(
        'id'        => '',
        'cat_id'    => '0',
        'title'     => '',
        'descr'     => '',
        'image'     => '',
        'middle'    => '',
        'thumb'     => '',
        'hidden'    => '0',
        'main'      => '0',
    );

    function createTables()
    {
        if ( ! $this->isExistTable(DBCATGALLERY) ) {
            $this->db->query("CREATE TABLE `".DBCATGALLERY."` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `cat_id` int(11) NOT NULL DEFAULT '0',
              `title` varchar(250) NOT NULL DEFAULT '',
              `descr` varchar(250) NOT NULL DEFAULT '',
              `image` varchar(250) NOT NULL DEFAULT '',
              `middle` varchar(250) NOT NULL DEFAULT '',
              `thumb` varchar(250) NOT NULL DEFAULT '',
              `hidden` tinyint(4) NOT NULL DEFAULT '0',
              `main` tinyint(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        }
    }

    function find( $id )
    {
        if ( ! isset( $this->data[$id] ) ) {
            $query = "SELECT * FROM ".DBCATGALLERY." WHERE id = {$id} LIMIT 1";
            $data = $this->db->fetch( $query );
            $this->data[$id] = $data;
        }
        return $this->data[$id];
    }

    /**
     * Вернет галлерею для продукта
     * @param $prod_id
     * @param $hidden
     * @return array
     */
    function findGalleryByProduct( $prod_id, $hidden = 1 )
    {
        if ( ! isset( $this->data_all[ $prod_id ] ) ) {
            $query =
                "SELECT *
                FROM ".DBCATGALLERY."
                WHERE cat_id = {$prod_id}";
            $this->data_all[ $prod_id ] = $this->db->fetchAll( $query, true );
        }
        $ret = array();
        if (is_array($this->data_all[ $prod_id ])) {
            foreach( $this->data_all[ $prod_id ] as $data ) {
                if ( $hidden || (!$hidden && !$data['hidden']) ) {
                    $ret[ $data['id'] ] = $data;
                }
            }
        }
        return $ret;
    }

    function insert()
    {
        unset( $this->data['id'] );
        $this->data['id'] = $this->db->insert( DBCATGALLERY, $this->data );
        return $this->data['id'];
    }

    function update()
    {
        if ( ! empty( $this->data['id'] ) ) {
            return $this->db->update( DBCATGALLERY, $this->data, " id = {$this->data['id']} " );
        }
        return false;
    }

    /**
     * Удалить изображения и запись из базы
     * @param int $id
     * @return void
     */
    function delete( $id )
    {
        $data = $this->db->fetch("SELECT * FROM ".DBCATGALLERY." WHERE id = {$id} LIMIT 1");
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
            $this->db->delete( DBCATGALLERY, " id = {$id} " );
        }
    }

    /**
     * Установить изображения по умолчанию
     * @param $id
     * @param $cat
     * @return void
     */
    function setDefault( $id, $cat )
    {
        $data = $this->db->fetch("SELECT * FROM ".DBCATGALLERY." WHERE id = {$id} LIMIT 1");
        $this->db->update( DBCATGALLERY, array('main'=>0), "cat_id = {$cat}", '' );
        if ( $data['main'] ) {
            //$this->db->update( DBCATALOG, array('image'=>'', 'thumb'=>''), "id = {$cat}" );
        } else {
            $this->db->update( DBCATGALLERY, array('main'=>1), "id = {$id}", 1 );
            //$this->db->update( DBCATALOG, array('image'=>$data['image'], 'thumb'=>$data['thumb']), "id = {$cat}" );
        }
    }
}