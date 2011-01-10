<?php
/**
 * Форма правки шаблона
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Forms_Templates_Edit extends form_Form
{
    function __construct()
    {
        parent::__construct(array(
            'name'      => 'temlates',
            'class'     => 'standart',
            'fields'    => array(
                'id'        => array('type'=>'int', 'hidden'),
                'name'      => array('type'=>'text','label'=>'Наименование', 'required'),
                'description'=> array('type'=>'text','label'=>'Описание', 'value'=>'', 'required'),
                'template'  => array('type'=>'textarea','label'=>'Шаблон', 'class'=>'plain', 'required'),
                'update'    => array('type'=>'date','label'=>'Дата обновления', 'value'=>time(), 'hidden'),
                
                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}
