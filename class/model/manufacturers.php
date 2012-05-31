<?php
/**
 * Модель Manufacturers
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Model_Manufacturers extends Sfcms_Model
{

    /**
     * @return Forms_Manufacturers_Edit
     */
    public function getForm()
    {
        return new Forms_Manufacturers_Edit();
    }
}