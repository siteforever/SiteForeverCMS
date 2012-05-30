<?php
/**
 * Категории новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Model_NewsCategory extends Sfcms_Model
{
    /** @var Forms_News_Category */
    private $form = null;

    /**
     * @return Forms_News_Category
     */
    function getForm()
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
