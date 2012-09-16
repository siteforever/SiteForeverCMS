<?php
/**
 * Правка раздела каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Forms_Catalog_Edit extends Form_Form
{
    /**
     * Создание формы
     */
    public function __construct()
    {
        /** @var $model Model_Catalog */
        $model   = Sfcms_Model::getModel( 'Catalog' );
        $parents = $model->getCategoryList();

        $manufModel = Sfcms_Model::getModel('Manufacturers');
        $manufacturers = $manufModel->findAll(array('order'=>'name'));
        $manufArray = array();
        foreach( $manufacturers as $manuf ) {
            $manufArray[ $manuf->id ] = $manuf->name;
        }
//        $this->getField('manufacturer')->setVariants( $manufArray );

        parent::__construct(array(
                    'name'  => 'catalog',
                    'title' => 'Раздел каталога',
                    'class' => 'form-horizontal',
                    'action'=> App::getInstance()->getRouter()->createServiceLink('catalog','save'),
                    'fields'=> array(

                        'id'        => array('type'=>'hidden', 'value'=>'0'),
                        'cat'       => array('type'=>'hidden', 'value'=>'1'),

                        'name'      => array('type'=>'text', 'label'=>'Наименование','required'),
                        'parent'    => array(
                            'type'      => 'select',
                            'label'     => 'Раздел',
                            'value'     => '0',
                            'variants'  => $parents,
                        ),

                        'path'      => array('type'=>'hidden'),

                        'articul'   => array('type'=>'text', 'label'=>'Артикул', 'value'=>'', 'hidden'),
                        'price1'    => array('type'=>'text', 'label'=>'Цена роз.', 'value'=>'0', 'hidden'),
                        'price2'    => array('type'=>'text', 'label'=>'Цена опт.', 'value'=>'0', 'hidden'),
                        'manufacturer' => array(
                            'type'=>'select', 'label'=>'Производитель', 'value'=>'0', 'hidden',
                            'variants' => $manufArray,
                        ),
                        'p0'        => array('type'=>'text', 'label'=>'Параметр 0'),
                        'p1'        => array('type'=>'text', 'label'=>'Параметр 1'),
                        'p2'        => array('type'=>'text', 'label'=>'Параметр 2'),
                        'p3'        => array('type'=>'text', 'label'=>'Параметр 3'),
                        'p4'        => array('type'=>'text', 'label'=>'Параметр 4'),
                        'p5'        => array('type'=>'text', 'label'=>'Параметр 5'),
                        'p6'        => array('type'=>'text', 'label'=>'Параметр 6'),
                        'p7'        => array('type'=>'text', 'label'=>'Параметр 7'),
                        'p8'        => array('type'=>'text', 'label'=>'Параметр 8'),
                        'p9'        => array('type'=>'text', 'label'=>'Параметр 9'),

                        'text'      => array('type'=>'textarea', 'label'=>'Описание'),

                        'sort_view' => array(
                            'type'=>'radio', 'label'=>'Выводить опции сортировки', 'value'=>'1',
                            'variants'=>array('1'=>'Выводить','0'=>'Не выводить',),
                        ),

                        'top'       => array('type'=>'radio', 'label'=>'Всегда в начале', 'value'=>'0', 'hidden',
                                             'variants' => array('1'=>'Да','0'=>'Нет',),
                        ),
                        'byorder'   => array('type'=>'radio', 'label'=>'Под заказ', 'value'=>'0', 'hidden',
                                             'variants' => array('1'=>'Да','0'=>'Нет',),
                        ),
                        'novelty'   => array('type'=>'radio', 'label'=>'Новинка', 'value'=>'0',
                                             'variants' => array('1'=>'Да','0'=>'Нет',),
                        ),
                        'absent'    => array('type'=>'radio', 'label'=>'Отсутствует', 'value'=>'0', 'hidden',
                                             'variants' => array('1'=>'Да','0'=>'Нет',),
                        ),

                        'hidden'    => array(
                            'type'      => 'radio',
                            'label'     => 'Скрытое',
                            'value'     => '0',
                            'variants'  => array('1'=>'Да','0'=>'Нет',),
                        ),
                        'protected' => array(
                            'type'      => 'radio',
                            'label'     => 'Защита страницы',
                            'value'     => USER_GUEST,
                            'variants'  => Sfcms_Model::getModel('User')->getGroups()
                        ),
                        'deleted' => array('type'=>'hidden','value'=>'0'),
                        'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
                    ),
                ));
    }
}
