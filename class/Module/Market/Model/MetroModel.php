<?php
/**
 * Модель Metro
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms\Model;

class MetroModel extends Model
{

    public function tableClass()
    {
        return 'Data_Table_Metro';
    }

    public function objectClass()
    {
        return 'Data_Object_Metro';
    }

}