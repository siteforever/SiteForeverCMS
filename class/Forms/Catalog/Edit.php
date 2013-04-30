<?php
/**
 * Правка раздела каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

use Module\Catalog\Model\CatalogModel;
use Sfcms\Model;

class Forms_Catalog_Edit extends \Sfcms\Form\Form
{
    protected $filter = null;

    /**
     * Создание формы
     */
    public function __construct()
    {
        /** @var $model CatalogModel */
        $model   = Model::getModel( 'Catalog' );
        $parents = $model->getCategoryList();

        $manufModel    = Model::getModel( 'Manufacturers' );
        $manufacturers = $manufModel->findAll( array( 'order'=> 'name' ) );
        $manufArray    = array('Не выбрано') + $manufacturers->column( 'name' );

        $materialModel = Model::getModel( 'Material' );
        $materials     = $materialModel->findAll( array( 'cond'=>'active=1', 'order'=> 'name' ) );
        $materialArray = array('Не выбрано') + $materials->column( 'name' );

        $typeModel     = Model::getModel('Module\Catalog\Model\TypeModel');
        $types         = $typeModel->findAll(array('order'=>'name'));

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
                    'required',
                ),

                'type_id'   => array(
                    'type' => 'select',
                    'label' => 'Тип товара',
                    'value' => '0',
                    'variants' => array('Не выбрано') + $types->column('name'),
                    'required',
                ),

                'path'      => array('type'=>'hidden'),

                'articul'   => array('type'=>'text', 'label'=>'Артикул', 'value'=>'', 'hidden'),
                'price1'    => array('type'=>'text', 'label'=>'Цена роз.', 'value'=>'0', 'hidden'),
                'price2'    => array('type'=>'text', 'label'=>'Цена опт.', 'value'=>'0', 'hidden'),
                'manufacturer' => array(
                    'type'=>'select', 'label'=>'Производитель', 'value'=>'0', 'hidden',
                    'variants' => $manufArray,
                ),
                'material' => array(
                    'type'=>'select', 'label'=>'Материал', 'value'=>'0', 'hidden',
                    'variants' => $materialArray,
                ),
                'gender'    => array(
                    'type'=>'select', 'label'=>'Пол',
                    'value' => '2',
                    'variants'=>array('0'=>'Ж','1'=>'М','2'=>'Уни'),
                    'require',
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

                'sale'      => array(
                    'type' => 'float', 'label' => 'Скидка, %',
                ),
                'sale_start'      => array(
                    'type' => 'date', 'label' => 'Начало скидки', 'value' => time(),
                ),
                'sale_stop'      => array(
                    'type' => 'date', 'label' => 'Конец скидки', 'value' => time(),
                ),

                'top'       => array('type'=>'radio', 'label'=>'Вывод в топе', 'value'=>'0',
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
                    'variants'  => Model::getModel('User')->getGroups()
                ),
                'deleted' => array('type'=>'hidden','value'=>'0'),
                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

    protected function loadFilter()
    {
        if ( null === $this->filter ) {
            if ( @file_exists( ROOT . '/protected/filters.php' ) ) {
                $this->filter = include( ROOT . '/protected/filters.php' );
            }
        }
        return $this->filter;
    }

    /**
     * Применит фильтр, если есть, к полям формы
     * @param $parentId
     */
    public function applyFilter( $parentId )
    {
        $catalogFinder = Model::getModel( 'Catalog' );

        $pitem   = $catalogFinder->find( $parentId );
        $fvalues = null;
        if ( $this->loadFilter() ) {
            while ( $pitem && ! $this->filter->getFilter( $pitem->id ) ) {
                if ( $pitem->parent ) {
                    $pitem = $catalogFinder->find( $pitem->parent );
                } else {
                    $pitem = false;
                }
            }
            $pitem && $fvalues = $this->filter->getFilter( $pitem->id );
        }

    }

    /**
     * Заполнит метки полей формы значениями из категории, отображающей тип товара
     * @param array $properties
     * @param Sfcms_Filter $fvalues
     */
    public function applyProperties( array $properties, $fvalues = null )
    {
        foreach( $properties as $k => $p ) {
            if ( preg_match('/^p(\d+)$/', $k, $m)) {
                $field = $this->getField($k);
                trim($p) ? $field->setLabel($p) : $field->hide();

                /** @var Sfcms_Filter_Group $fGroup */
                if ($fvalues && $fGroup = $fvalues->getFilterGroup($m[1])) {
                    if (is_array($fGroup->getData()) && !$field->getValue()) {
                        $this->getField($k)->setValue(
                            str_ireplace(array('Все|','All|'), '', implode('|', $fGroup->getData()))
                        );
                    }
                }
            }
        }
    }
}
