<?php
/**
 * Модель шаблонов
 */
namespace Module\System\Model;

use Sfcms_Model;
use Form_Form;
use Forms_Templates_Edit;

class TemplatesModel extends Sfcms_Model
{
    /**
     * Форма редактирования
     * @var Form_Form
     */
    private $form;

    protected $table;

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_Templates';
    }


    public function objectClass()
    {
        return 'Data_Object_Templates';
    }

    /**
     * Искать шаблон по названию
     * @param string $name
     */
    public function findByName( $name )
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
    public function getForm()
    {
        if ( !isset($this->form) ) 
        {
            $this->form = new Forms_Templates_Edit();
        }
        return $this->form;
    }


}