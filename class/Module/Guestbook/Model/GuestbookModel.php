<?php
/**
 * Модель Guestbook
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Guestbook\Model;

use Sfcms\Model;

class GuestbookModel extends Model
{
    public function relation()
    {
        return array(
            'Category' => array( self::BELONGS, 'Page', 'link' ),
        );
    }
}