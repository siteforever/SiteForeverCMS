<?php
/**
 * Правка раздела каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Module\Catalog\Form;

use App;
use Module\Catalog\Model\CatalogModel;
use Sfcms\Data\DataManager;
use Sfcms\Form\Form;
use Sfcms\Model;

class CatalogForm extends Form
{
    protected $filter = null;

    protected $dataManager;

    /**
     * Создание формы
     */
    public function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
        /** @var $model CatalogModel */
        $model   = $dataManager->getModel( 'Catalog' );
        $parents = $model->getCategoryList();

        $manufModel    = $dataManager->getModel( 'Manufacturers' );
        $manufacturers = $manufModel->findAll( array( 'order'=> 'name' ) );
        $manufArray    = array('catalog.not_selected') + $manufacturers->column( 'name' );

        $materialModel = $dataManager->getModel( 'Material' );
        $materials     = $materialModel->findAll( array( 'cond'=>'active=1', 'order'=> 'name' ) );
        $materialArray = array('catalog.not_selected') + $materials->column( 'name' );

        $typeModel     = $dataManager->getModel('Catalog.ProductType');
        $types         = $typeModel->findAll(array('order'=>'name'));

        parent::__construct(array(
            'name'  => 'catalog',
            'class' => 'form-horizontal',
            'action'=> App::cms()->getRouter()->createServiceLink('catalog','save'),
            'fields'=> array(

                'id'        => array('type'=>'hidden', 'value'=>'0'),
                'cat'       => array('type'=>'hidden', 'value'=>'1'),

                'name'      => array('type'=>'text', 'label'=>'catalog.name','required'),
                'parent'    => array(
                    'type'      => 'select',
                    'label'     => 'catalog.category',
                    'value'     => '0',
                    'variants'  => $parents,
                    'required',
                ),

                'type_id'   => array(
                    'type' => 'select',
                    'label' => 'catalog.product_type',
                    'value' => '0',
                    'variants' => array('catalog.not_selected') + $types->column('name'),
                    'required',
                ),

                'path'      => array('type'=>'hidden'),

                'articul'   => array('type'=>'text', 'label'=>'catalog.article', 'value'=>'', 'hidden'),
                'price1'    => array('type'=>'text', 'label'=>'catalog.price_retail', 'value'=>'0', 'hidden'),
                'price2'    => array('type'=>'text', 'label'=>'catalog.price_wholesale', 'value'=>'0', 'hidden'),
                'manufacturer' => array(
                    'type'=>'select', 'label'=>'catalog.manufacturer', 'value'=>'0', 'hidden',
                    'variants' => $manufArray,
                ),
                'material' => array(
                    'type'=>'select', 'label'=>'catalog.material', 'value'=>'0', 'hidden',
                    'variants' => $materialArray,
                ),
                'gender'    => array(
                    'type'=>'select', 'label'=>'catalog.gender',
                    'value' => '2',
                    'variants'=>array('0'=>'catalog.female','1'=>'catalog.male','2'=>'catalog.unisex'),
                    'require',
                ),
                'qty'       => array('type'=>'int', 'label'=>'catalog.qty'),
                'p0'        => array('type'=>'text', 'label'=>'catalog.param_0'),
                'p1'        => array('type'=>'text', 'label'=>'catalog.param_1'),
                'p2'        => array('type'=>'text', 'label'=>'catalog.param_2'),
                'p3'        => array('type'=>'text', 'label'=>'catalog.param_3'),
                'p4'        => array('type'=>'text', 'label'=>'catalog.param_4'),
                'p5'        => array('type'=>'text', 'label'=>'catalog.param_5'),
                'p6'        => array('type'=>'text', 'label'=>'catalog.param_6'),
                'p7'        => array('type'=>'text', 'label'=>'catalog.param_7'),
                'p8'        => array('type'=>'text', 'label'=>'catalog.param_8'),
                'p9'        => array('type'=>'text', 'label'=>'catalog.param_9'),

                'text'      => array('type'=>'textarea', 'label'=>'catalog.description'),

                'sort_view' => array(
                    'type'=>'checkbox', 'label'=>'catalog.show_sorting', 'value'=>'1'),

                'sale'      => array(
                    'type' => 'float', 'label' => 'catalog.sale', 'notice' => 'Для указания скидки в %, надо указать 10%',
                ),
                'sale_start'      => array(
                    'type' => 'date', 'label' => 'catalog.sale_start', 'value' => time(),
                ),
                'sale_stop'      => array(
                    'type' => 'date', 'label' => 'catalog.sale_end', 'value' => time(),
                ),

                'top'       => array('type'=>'checkbox', 'label'=>'catalog.show_main', 'value'=>'0'),
                'byorder'   => array('type'=>'checkbox', 'label'=>'catalog.for_order', 'value'=>'0', 'hidden'),
                'novelty'   => array('type'=>'checkbox', 'label'=>'catalog.new', 'value'=>'0'),
                'absent'    => array('type'=>'checkbox', 'label'=>'catalog.absent', 'value'=>'0'),

                'hidden'    => array(
                    'type'      => 'checkbox',
                    'label'     => 'catalog.hidden',
                    'value'     => '0',
                ),
                'protected' => array(
                    'type'      => 'select',
                    'label'     => 'catalog.protected',
                    'value'     => USER_GUEST,
                    'variants'  => $dataManager->getModel('User')->getGroups()
                ),
                'deleted' => array('type'=>'hidden','value'=>'0'),
                'submit'    => array('type'=>'submit', 'value'=>'catalog.save'),
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
        $catalogFinder = $this->dataManager->getModel( 'Catalog' );

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
     * @param \Sfcms_Filter $fvalues
     */
    public function applyProperties( array $properties, $fvalues = null )
    {
        foreach( $properties as $k => $p ) {
            if ( preg_match('/^p(\d+)$/', $k, $m)) {
                $field = $this->getChild($k);
                trim($p) ? $field->setLabel($p) : $field->hide();

                /** @var \Sfcms_Filter_Group $fGroup */
                if ($fvalues && $fGroup = $fvalues->getFilterGroup($m[1])) {
                    if (is_array($fGroup->getData()) && !$field->getValue()) {
                        $this->getChild($k)->setValue(
                            str_ireplace(array('Все|','All|'), '', implode('|', $fGroup->getData()))
                        );
                    }
                }
            }
        }
    }
}
