<?php
/**
 * Модель Guestbook
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Model_Guestbook extends Sfcms_Model
{
    public function relation()
    {
        return array(
            'Category' => array( self::BELONGS, 'Page', 'link' ),
        );
    }

}