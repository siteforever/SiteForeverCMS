<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
namespace Module\Catalog\Model;

use Sfcms\Model;
use Sfcms\Data\Collection;
use Module\Catalog\Object\Gallery;

class GalleryModel extends Model
{
    /**
     * Вернет галлерею для продукта
     * @param $prod_id
     * @param $hidden
     * @return Collection
     */
    public function findGalleryByProduct( $prod_id, $hidden = 1 )
    {
        if ( $hidden ) {
            $cond = 'cat_id = :cat_id AND hidden = :hidden';
        } else {
            $cond = 'cat_id = :cat_id';
        }
        return $this->findAll(array(
            'cond'  => $cond,
            'params'=> array(':cat_id'=>$prod_id, ':hidden'=>$hidden),
        ));
    }

    /**
     * Удалить изображения и запись из базы
     * @param int $id
     * @return void
     */
    public function remove( $id )
    {
        $data   = $this->find( $id );
        //$data = $this->db->fetch("SELECT * FROM {$this->getTable()} WHERE id = {$id} LIMIT 1");
        if ( $data ) {
            if ( $data['thumb'] && file_exists(ROOT.$data['thumb']) ) {
                @unlink ( ROOT.$data['thumb'] );
            }
//            if ( $data['middle'] && file_exists(ROOT.$data['middle']) ) {
//                @unlink ( ROOT.$data['middle'] );
//            }
            if ( $data['image'] && file_exists(ROOT.$data['image']) ) {
                @unlink ( ROOT.$data['image'] );
            }
            $this->delete( $id );
//            $data->markDeleted();
        }
    }

    /**
     * Установить изображения по умолчанию
     * @param $id
     * @param $cat
     * @return void
     */
    public function setDefault( $id, $cat )
    {
        $images = $this->findGalleryByProduct( $cat, null );
        foreach ( $images as $obj ) {
            /** @var $obj Gallery */
            $obj->main = 0;
        }
        /** @var $image Gallery */
        $image = $this->find( $id );
        $image->main = 1;
    }
}
