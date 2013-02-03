<?php
/**
 * Модель шаблонов
 */
namespace Module\System\Model;

use Sfcms\Form\Form;
use Sfcms\Model;
use Forms_Templates_Edit;

class TemplatesModel extends Model
{
    /**
     * Форма редактирования
     * @var Form
     */
    private $form;

    protected $table;

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
     * @return Form
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