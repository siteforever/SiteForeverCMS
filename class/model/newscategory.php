<?php
/**
 * Категории новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class model_NewsCategory extends Model
{
    private $form = null;

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_news_Category();
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
