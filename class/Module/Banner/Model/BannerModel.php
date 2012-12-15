<?php
/**
 * Модель баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */

namespace Module\Banner\Model;

use Sfcms_Model;
use Forms_Banners_Banner;

class BannerModel extends Sfcms_Model
{
    /**
     * @var Forms_Banners_Banner
     */
    private $_form  = null;

     /**
     * @return Forms_Banners_Banner
     */
    function getForm()
    {
        if (  null === $this->_form ) {
            $this->_form = new Forms_Banners_Banner();
        }
        return $this->_form;
    }

    /**
     * @param $id
     * @return bool
     */
    public function onDeleteStart( $id = null )
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

    public function tableClass()
    {
        return 'Data_Table_Banner';
    }

    public function objectClass()
    {
        return 'Data_Object_Banner';
    }
}
