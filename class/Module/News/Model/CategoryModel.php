<?php
/**
 * Категории новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Model;

use Sfcms_Model;
use Forms_News_Category;

class CategoryModel extends Sfcms_Model
{
    /** @var Forms_News_Category */
    private $form = null;

    public function relation()
    {
        return array(
            'Page' => array( self::BELONGS, 'Page', 'link' ),
        );
    }

    /**
     * @return Forms_News_Category
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_News_Category();
        }
        return $this->form;
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_NewsCategory';
    }

    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_NewsCategory';
    }
}
