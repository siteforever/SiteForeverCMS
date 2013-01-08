<?php
/**
 * Модель маршрутов
 */
namespace Module\System\Model;

use Sfcms\Model;

class RoutesModel extends Model
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