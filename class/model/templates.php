<?php
/**
 * Модель шаблонов
 */
class model_Templates extends Model
{
    /**
     * Форма редактирования
     * @var form_Form
     */
    private $form;

    protected $table;

    function createTables()
    {
        $this->table = new Data_Table_Templates();

        if ( ! $this->isExistTable($this->table) ) {
            $this->db->query($this->table->getCreateTable());
        }
    }

    /**
     * Искать шаблон по названию
     * @param string $name
     */
    function findByName( $name )
    {
        $data = $this->find(array(
             'cond'     => 'name = :name',
             'params'   => array(':name'=>$name),
        ));
        return $data;
    }

    /**
     * Вернет объект формы
     * @return form_Form
     */
    function getForm()
    {
        if ( !isset($this->form) ) 
        {
            $this->form = new Forms_Templates_Edit();
        }
        return $this->form;
    }    
}