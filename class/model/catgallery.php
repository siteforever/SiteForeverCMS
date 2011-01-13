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

    /**
     * Вернет галлерею для продукта
     * @param $prod_id
     * @param $hidden
     * @return array
     */
    function findGalleryByProduct( $prod_id, $hidden = 1 )
    {
        if ( ! isset( $this->data_all[ $prod_id ] ) ) {
            $this->data_all[ $prod_id ] = $this->findAll(array(
                'cond'  => 'cat_id = :cat_id AND hidden = :hidden',
                'params'=> array(':cat_id'=>$prod_id, ':hidden'=>$hidden),
            ));
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
        $this->data['id'] = $this->db->insert( $this->getTable(), $this->data );
        return $this->data['id'];
    }

    function update()
    {
        if ( ! empty( $this->data['id'] ) ) {
            return $this->db->update( $this->getTable(), $this->data, " id = {$this->data['id']} " );
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
        $data   = $this->find( $id );
        //$data = $this->db->fetch("SELECT * FROM {$this->getTable()} WHERE id = {$id} LIMIT 1");
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
        $data = $this->find( $id );
        //$this->db->update( DBCATGALLERY, array('main'=>0), "cat_id = {$cat}", '' );
        if ( $data['main'] ) {
            $this->set('main', 0);
            //$this->db->update( DBCATALOG, array('image'=>'', 'thumb'=>''), "id = {$cat}" );
        } else {
            $this->set('main', 1);
            //$this->db->update( DBCATGALLERY, array('main'=>1), "id = {$id}", 1 );
            //$this->db->update( DBCATALOG, array('image'=>$data['image'], 'thumb'=>$data['thumb']), "id = {$cat}" );
        }
        $this->set('cat_id', $cat);
        $this->save();
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_CatGallery';
    }
}
