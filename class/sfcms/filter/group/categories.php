<?php
/**
 * Список категорий
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_Filter_Group_Categories extends Sfcms_Filter_Group
{
    /**
     * Заполнить данные
     * @param int $id
     * @param Sfcms_Model $model
     */
    public function fillData( $id, Sfcms_Model $model )
    {
        $result = null;
        $categories = $model->findAll(
            'parent = ? AND deleted = 0 AND hidden = 0 AND cat = 1',
            array( $id ), 'pos DESC'
        );
        if ( $categories->count() ) {
            $result = array( 0 => 'Все' );
            foreach ( $categories as $cat ) {
                $result[ $cat->id ] = $cat->name;
            }
        }
        $this->setData( $result );
    }
}
