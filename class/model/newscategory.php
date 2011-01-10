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

    function createTables()
    {
        $this->table    = new Data_Table_NewsCategory();
        if ( ! $this->isExistTable( $this->table ) ) {
            $this->db->query($this->table->getCreateTable());
        }
    }


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
}
