<?php
/**
 * Модель для изображений галереи
 */

namespace Module\Gallery\Model;

use Sfcms_Model;
use Forms_Gallery_Image;

class GalleryModel extends Sfcms_Model
{
    protected $form;

    public function relation()
    {
        return array(
            'Category' => array(self::BELONGS, 'GalleryCategory', 'category_id'),
        );
    }


    public function tableClass()
    {
        return 'Data_Table_Gallery';
    }

    public function objectClass()
    {
        return 'Data_Object_Gallery';
    }

    /**
     * Получает следующую позицию для сортировки
     * @param $category_id
     * @return string
     */
    public function getNextPosition( $category_id )
    {
        return $this->db->fetchOne(
             "SELECT MAX(pos)+1 "
            ."FROM `{$this->getTable()}` "
            ."WHERE category_id = ? "
            ."LIMIT 1",
            array($category_id)
        );
    }

    /**
     * Удалить изображения перед удаление объекта
     * @param int $id
     * @return boolean
     */
    public function onDeleteStart( $id = null )
    {
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
            return true;
        }
        return false;
    }

    /**
     * Пересортировка изображений
     * @return int
     */
    public function reposition()
    {
        $positions = $this->request->get('positions');
        $new_pos = array();
        foreach ( $positions as $pos => $id ) {
            $new_pos[] = array('id'=>$id, 'pos'=>$pos);
        }
        $imgObj     = $this->find( $positions[0] );
        $catModel   = self::getModel('GalleryCategory');
        $catObj     = $catModel->find( $imgObj->get('category_id') );
        $catObj->set('thumb', $imgObj->get('thumb'));
        $catObj->save();
        return $this->db->insertUpdateMulti($this->getTable(), $new_pos);
    }

    /**
     * Переключение активности изображения
     * @param int $id
     * @return bool|int
     */
    public function hideSwitch( $id )
    {
        if( ! $obj = $this->find( $id ) ) {
            return false;
        }
        if ( $obj['hidden'] ) {
            $obj['hidden'] = '0';
        }
        else {
            $obj['hidden'] = '1';
        }
        return $obj['hidden'] ? 1 : 2;
    }

    /**
     * @return Forms_Gallery_Image
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_Gallery_Image();
        }
        return $this->form;
    }
}
