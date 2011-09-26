<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 15.09.11
 * Time: 13:09
 * To change this template use File | Settings | File Templates.
 * Модель баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */
 
class Model_Banner extends Model
{
     /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_banners_banner();
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
