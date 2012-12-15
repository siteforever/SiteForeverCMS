<?php
/**
 * Модель Metro
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms_Model;

class MetroModel extends Sfcms_Model
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