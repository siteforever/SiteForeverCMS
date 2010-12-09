<?php

class model_galleryCategory extends Model
{
    protected $table;

    /**
     * @var form_Form
     */
    protected $form;


    function createTables()
    {
        $this->table    = DBPREFIX.'gallery_category';

        if ( ! $this->isExistTable( $this->table ) ) {
            $this->db->query("
                CREATE TABLE `{$this->table}` (
                  `id` int(11) NOT NULL auto_increment,
                  `name` varchar(200) NOT NULL default '',
                  `middle_method` tinyint(4) NOT NULL default '1',
                  `middle_width` int(11) NOT NULL default '200',
                  `middle_height` int(11) NOT NULL default '200',
                  `thumb_method` tinyint(4) NOT NULL default '1',
                  `thumb_width` int(11) NOT NULL default '100',
                  `thumb_height` int(11) NOT NULL default '100',
                  PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8
            ");
        }
    }

    function find( $id )
    {
        $this->data = $this->db->fetch("SELECT * FROM {$this->table} WHERE id = {$id} LIMIT 1");
        return $this->data;
    }

    function findAll()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    function insert()
    {
        $id = $this->db->insert($this->table, $this->data);
        $this->set('id', $id);
        return $id;
    }

    function update()
    {
        return $this->db->update($this->table, $this->data, "id = {$this->data['id']}");
    }

    /**
     * Удаление категории
     * @param  $id
     * @return
     */
    function delete( $id = null )
    {
        if ( is_null( $id ) ) {
            $id = $this->data['id'];
        }

        /**
         * @var model_gallery $gallery
         */
        $gallery = self::getModel('model_Gallery');

        $cat = $this->find( $id );
        if ( $cat ) {
            $images = $gallery->findAll(array('category_id = '.$cat['id']));
            foreach ( $images as $img ) {
                $gallery->delete( $img['id'] );
            }

            //print 'dir:'.ROOT.$this->config->get('gallery.dir').DS.substr( '0000'.$cat['id'], -4, 4 );
            if ( @rmdir( ROOT.$this->config->get('gallery.dir').DS.substr( '0000'.$cat['id'], -4, 4 ) ) ) {
                $this->db->delete($this->table, "id = {$cat['id']}");
            }
        }

        return;
    }

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_gallery_category();
        }
        return $this->form;
    }
}
