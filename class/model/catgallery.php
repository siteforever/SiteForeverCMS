<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Model_CatGallery extends Model
{
    // Массив, проиднексированный по продуктам
    protected $data_all = array();

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
                    $ret[ $data->getId() ] = $data;
                }
            }
        }
        return $ret;
    }

    /**
     * Удалить изображения и запись из базы
     * @param int $id
     * @return void
     */
    function remove( $id )
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
            $data->markDeleted();
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
        if ( $data['main'] ) {
            $data['main'] = 0;
        } else {
            $data['main'] = 1;
        }
        $data['cat_id'] = $cat;
        $this->save( $data );
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_CatGallery';
    }

    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_CatGallery';
    }
}
