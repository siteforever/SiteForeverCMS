<?php
/**
 * Модель маршрутов
 */
namespace Module\System\Model;

use Sfcms_Model;

class RoutesModel extends Sfcms_Model
{
    public function tableClass()
    {
        return 'Data_Table_Routes';
    }

    public function objectClass()
    {
        return 'Data_Object_Routes';
    }

}