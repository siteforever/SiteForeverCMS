<?php
/**
 * Модель баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */

namespace Module\Banner\Model;

use Sfcms\Model;

class BannerModel extends Model
{
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
