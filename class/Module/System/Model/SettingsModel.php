<?php
/**
 * Модель настроек сайта
 * @author  Nikolay Ermin <nikolay@ermin.ru>
 */
namespace Module\System\Model;

use Sfcms\Model;

class SettingsModel extends Model
{

    public function tableClass()
    {
        return 'Data_Table_Model';
    }

    public function objectClass()
    {
        return 'Data_Object_Model';
    }

}