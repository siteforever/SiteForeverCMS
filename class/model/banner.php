<?php
/**
 * Модель баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */
 
class Model_Banner extends Model
{
     /**
     * @return Form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_Banners_Banner();
        }
        return $this->form;
    }

    public function onDeleteStart( $id )
    {
        $data = $this->find( $id );
        if ( $data ) {
            if ( $data['path'] && file_exists(ROOT.$data['path']) ) {
                @unlink ( ROOT.$data['path'] );
            }
            return true;
        }
        return false;
    }
}
