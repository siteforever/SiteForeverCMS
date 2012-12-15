<?php
/**
 * Модель Guestbook
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Guestbook\Model;

use Sfcms_Model;

class GuestbookModel extends Sfcms_Model
{
    public function relation()
    {
        return array(
            'Category' => array( self::BELONGS, 'Page', 'link' ),
        );
    }

    public function tableClass()
    {
        return 'Data_Table_Guestbook';
    }

    public function objectClass()
    {
        return 'Data_Object_Guestbook';
    }

}