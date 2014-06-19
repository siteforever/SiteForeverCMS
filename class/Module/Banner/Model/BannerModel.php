<?php
/**
 * Модель баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */

namespace Module\Banner\Model;

use Sfcms\Model;
use Module\Banner\Form\BannerForm;

class BannerModel extends Model
{
    /**
     * @var BannerForm
     */
    private $_form  = null;

     /**
     * @return BannerForm
     */
    function getForm()
    {
        if (null === $this->_form) {
            $this->_form = new BannerForm($this->getDataManager());
        }
        return $this->_form;
    }

    /**
     * @param $id
     * @return bool
     */
    public function onDeleteStart($id = null)
    {
        $data = $this->find($id);
        if ($data) {
            if ($data['path'] && file_exists(ROOT . $data['path'])) {
                @unlink(ROOT . $data['path']);
            }
            return true;
        }
        return false;
    }
}
