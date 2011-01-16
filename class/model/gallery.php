<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 30.09.2010
 * Time: 23:56:10
 * To change this template use File | Settings | File Templates.
 */

class model_Gallery extends Model
{
    protected $form;
    
    /**
     * @return model_galleryCategory
     */
    function category()
    {
        return self::getModel('galleryCategory');
    }

    function getNextPosition( $category_id )
    {
        return $this->db->fetchOne(
            "SELECT MAX(pos)+1
            FROM {$this->table}
            WHERE category_id = :category_id
            LIMIT 1",
            array(':category_id'=>$category_id)
        );
    }

    /**
     * @param array $cond = array()
     * @param string $limit = ''
     * @return array
     *
     * $model = findAll(array('category_id = 10', 'hidden = 0'), '20, 10');
     */
    /*function findAll($cond = array(), $limit = '')
    {
        $where = '';
        if ( count($cond) ) {
            $where = ' WHERE '.implode(' AND ', $cond);
        }
        if ( $limit != '' ) {
            $limit = ' LIMIT '.$limit;
        }
        return $this->db->fetchAll("SELECT * FROM {$this->table} {$where} ORDER BY `pos` {$limit}");
    }*/

    /**
     * Количество по условию
     * @param array $cond
     * @return string
     */
    /*function getCount($cond = array())
    {
        $where = '';
        if ( count($cond) ) {
            $where = ' WHERE '.implode(' AND ', $cond);
        }
        return $this->db->fetchOne("SELECT COUNT(*) FROM {$this->table} {$where}");
    }*/

    /*function insert()
    {
        unset( $this->data['id'] );
        $id = $this->db->insert($this->table, $this->data);
        $this->set('id', $id);
        return $id;
    }*/

    /*function update()
    {
        return $this->db->update($this->table, $this->data, "id = {$this->data['id']}");
    }*/

    /**
     * Удалить изображения и запись из базы
     * @param int $id
     * @return void
     */
    function remove( $id )
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
            $data->markDeleted();
            //return $this->db->delete( $this->table, " id = {$id} " );
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
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_gallery_image();
        }
        return $this->form;
    }

    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_Gallery';
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_Gallery';
    }
}
