<?php
/**
 * Модель каталога
 * @author KelTanas
 */
namespace Module\Catalog\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Module\Page\Object\Page;
use Sfcms;
use Sfcms\JqGrid\Provider;
use Sfcms\Model;
use Module\Catalog\Object\Catalog;
use Sfcms\Data\Collection;
use Sfcms\db;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class CatalogModel extends Model implements EventSubscriberInterface
{
    /**
     * Массив, индексируемый по $parent
     * @var array
     */
    protected $parents = null;

    /**
     * Списков разделов в кэше
     * @var Sfcms\Data\Collection
     */
    protected $all = null;

    public $html = array();

    /**
     * @var Sfcms\Form\Form
     */
    protected $form = null;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'plugin.page-catalog.save.start' => array('pluginPageSaveStart', 0),
            'plugin.page-catalog.resort' => array('pluginPageResort', 0),
        );
    }

    public function relation()
    {
        return array(
            'Gallery'      => array(self::HAS_MANY, 'CatalogGallery', 'cat_id', 'order' => 'pos'),
            'Image'        => array(self::HAS_ONE,  'CatalogGallery', 'cat_id', 'where' => 'main = 1'),
            'Comments'     => array(self::HAS_MANY, 'CatalogComment', 'product_id',
                                    'order'=>'createdAt DESC', 'where' => array('deleted'=>0)),
            'Category'     => array(self::BELONGS, 'Catalog', 'parent'),
            'Manufacturer' => array(self::BELONGS, 'Manufacturers', 'manufacturer'),
            'Material'     => array(self::BELONGS, 'Material', 'material'),
            'Goods'        => array(self::HAS_MANY, 'Catalog', 'parent', 'where'=>array('cat'=>0)),
            'Page'         => array(self::HAS_ONE, 'Page', 'link', 'where' => array('controller' => 'catalog')),
            'Properties'   => array(
                self::HAS_MANY, 'ProductProperty', 'product_id',
                'with'  => array('Field'), 'order' => 'pos'
            ),
            'Type'         => array(self::BELONGS, 'ProductType', 'type_id'),
        );
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами каталога
     *
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function pluginPageSaveStart( Model\ModelEvent $event )
    {
        $this->log('triggered: ' . __METHOD__);
        /** @var $page Page */
        $page       = $event->getObject();
        $pageModel  = $event->getModel();

        /** @var $category Catalog */
        $category = null;
        if ($page->link) {
            $category = $this->find($page->link);
        }
        if (!$category) {
            $category = $this->createObject();
            $category->cat = 1;
            $this->save($category, true, true);
            $page->link = $category->getId();
        }

        // Надо скрыть или показать все товары в данной категории, если изменился уровень видимости категории
        if ($category->id
            && !($category->hidden == $page->hidden && $category->protected == $page->protected && $category->deleted == $page->deleted))
        {
            array_map(function($product) use ($page) {
                /** @var $product Catalog */
                $product->hidden = $page->hidden;
                $product->protected = $page->protected;
                $product->deleted = $page->deleted;
            },iterator_to_array($category->Goods));
        }

        /** @var $category Catalog */
        $category->name         = $page->name;
        $category->pos          = $page->pos;
        $category->hidden       = $page->hidden;
        $category->protected    = $page->protected;
        $category->deleted      = $page->deleted;

        if ($page->parent) {
            /** @var $parentPage Page */
            $parentPage = $pageModel->find($page->parent);
            if ($parentPage->controller == $page->controller && $parentPage->link) {
                $category->parent = $parentPage->link;
                $category->parent_uuid = $category->Category->uuid;
            } else {
                $category->parent = 0;
            }
        }

        $this->save($category, false, true);
        $category->Page = $page;
    }

    /**
     * Пересортировка
     *
     * Вызывается при пересортировке страниц.
     * Сюда передается объект страницы с новым параметром link.
     *
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function pluginPageResort( Model\ModelEvent $event )
    {
        $obj = $event->getObject();
        /** @var $catObj Catalog */
        $catObj = $this->find($obj->link);
        $catObj->pos = $obj->pos;
        $catObj->markDirty();
    }


    /**
     * Вызывается перед сохранением каталога
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function onSaveStart( Model\ModelEvent $event )
    {
        $obj = $event->getObject();
        // If object will update
        /** @var $obj Catalog */
        if ($obj->getId()) {
            $obj->path = $obj->getModel()->createSerializedPath($obj->getId());
        }

        if (!$obj->uuid) {
            $obj->uuid = Sfcms\UUID::v5(md5(__DIR__), bin2hex(uniqid()));
        }
        if ($obj->parent) {
            $obj->parent_uuid = $obj->Category->uuid;
        }

        if ( $obj->cat ) {
            // @todo Надо сделать слежение за изменением иерархии
            $objPage = $obj->Page;
            if ( $objPage ) {
                $objPage->name = $obj->name;
                $objPage->hidden = $obj->hidden;
                $objPage->protected = $obj->protected;
            }
        }
    }

    /**
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function onSaveSuccess( Model\ModelEvent $event )
    {
        /** @var $obj Catalog */
        $obj = $event->getObject();
        $model = $obj->getModel();

        // If object was just created
        if (!$obj->path) {
            $obj->path = $model->createSerializedPath($obj->getId());
        }
        $objPage = $obj->Page;
        if (null !== $objPage && !$objPage->link) {
            $objPage->link = $obj->id;
        }

        if ($obj->cat) {
            // @todo Надо сделать слежение за изменением иерархии
            if (null === $objPage) {
                $objPage = $model->getModel('Page')->createObject();
                $objPage->markNew();
                $objPage->name = $obj->name;
                $objPage->title = $obj->name;
                $objPage->pos = $obj->pos;
                $objPage->author = 1;
                $objPage->template = 'inner';
                $objPage->link = $obj->id;
                $objPage->controller = 'catalog';
                $objPage->action = 'index';
                $objPage->date = $objPage->update = time();
                $objPage->hidden = $obj->hidden;
                $objPage->protected = $obj->protected;
                if ($obj->parent) {
                    $parentPage = $obj->Category->Page;
                    if ($parentPage) {
                        $objPage->parent = $parentPage->id;
                    }
                }
            }
        }
    }



    /**
     * Вернет прямых потомков для раздела
     * @param int $parentId
     * @return array
     */
    public function getChildrenFor( $parentId )
    {
        if ( null === $this->parents ) {
            $this->createTree();
        }
        if ( ! isset( $this->parents[ $parentId ] ) ) {
            return array();
        }
        return $this->parents[$parentId];
    }


    /**
     * Вернет потомков всех поколений
     * @param int $parentId
     * @param int $level
     * @return array
     */
    public function getAllChildrensIds( $parentId, $level = 0 )
    {
        $level--;
        /** @var $child Catalog */
        $categoriesId = array();
        $children = $this->getChildrenFor( $parentId );
        foreach ( $children as $child ) {
            $categoriesId[] = $child->getId();
            if ( $level ) {
                $categoriesId = array_merge( $categoriesId, $this->getAllChildrensIds( $child->getId() ) );
            }
        }
        return $categoriesId;
    }

    /**
     * Искать все в список по фильтру по артикулу
     * @param string $filter
     *
     * @deprecated
     * @return array
     */
    public function findAllFiltered( $filter )
    {
        return $this->findAll(
            "deleted = 0 AND articul LIKE :filter",
            array( ':filter'=> '%' . $filter . '%' ),
            'cat DECS, pos DESC'
        );
    }

    /**
     * Искать список по родителю
     * @param int    $parent
     * @param string $limit
     *
     * @deprecated
     * @return array
     */
    public function findAllByParent( $parent, $limit = 'LIMIT 100' )
    {
        $list = $this->getDB()->fetchAll(
            "SELECT cat.*, COUNT(child.id) child_count "
            . "FROM {$this->getTable()} cat "
            . "   LEFT JOIN {$this->getTable()} child ON child.parent = cat.id AND child.deleted = 0 "
            . "WHERE cat.parent = :parent AND cat.deleted = 0 "
            . "GROUP BY cat.id "
            . "ORDER BY  cat.cat DESC, cat.pos DESC "
            . $limit,
            true, db::F_ASSOC, array( ':parent'=> $parent )
        );
        return $list;
    }

    /**
     * Искать категории по родителю
     * @param        $parent
     * @param string $limit
     *
     * @deprecated
     * @return array
     */
    public function findCatsByParent($parent, $limit = '')
    {
        $list = $this->getDB()->fetchAll(
            "SELECT cat.*, COUNT(sub.id) sub_count "
            . "FROM {$this->getTable()} cat  "
            . "    LEFT JOIN {$this->getTable()} sub ON sub.parent = cat.id "
            . "       AND sub.cat = 0 "
            . "       AND sub.deleted = 0 "
            . "       AND sub.hidden = 0 "
            . "WHERE cat.parent = '$parent' "
            . "   AND cat.cat = 1 "
            . "   AND cat.deleted = 0 "
            . "   AND cat.hidden = 0 "
            . "GROUP BY cat.id "
            . "{$limit}"
        );
        return $list;
    }

    /**
     * @return Catalog[]|Collection
     */
    public function findAllProducts()
    {
        return $this->findAll('deleted = 0 AND hidden = 0 AND protected = 0 AND cat = 0');
    }

    /**
     * Products, sorted by "top"
     * @param int $limit
     * @return array|Collection
     */
    public function findProductsSortTop($limit = 4)
    {
        return $this->with('Gallery')
            ->findAll(
                'deleted = 0 AND hidden = 0 AND protected = 0 AND cat = 0 AND top = 1 AND absent != 1',
                array(),
                'top DESC',
                $limit
            );
    }


    /**
     * Products, sorted by "novelty"
     * @param int $limit
     * @return array|Collection
     */
    public function findProductsSortNovelty($limit = 4)
    {
        return $this->with('Gallery')
            ->findAll(
                'deleted = 0 AND hidden = 0 AND protected = 0 AND cat = 0 AND novelty = 1 AND absent != 1',
                array(),
                'novelty DESC',
                $limit
            );
    }

    /**
     * Products on sale for current time
     *
     * @param int $limit
     *
     * @return array|Collection
     */
    public function findProductsWithSale($limit = 4)
    {
        return $this->findAll(
            join(' AND ', array(
                    '`deleted` = 0 AND `hidden` = 0 AND `protected` = 0',
                    '`cat` = 0 AND `absent` != 1',
                    '`sale` > 0 AND `sale_start` <= ? AND `sale_stop` >= ?',
                )),
            array(mktime(0, 0, 0), mktime(0, 0, 0)),
            'pos DESC',
            $limit);
    }


    /**
     * Поиск товаров по ключевой фразе
     *
     * @param $query
     *
     * @use GoodsController::searchAction
     *
     * @return QueryBuilder
     */
    public function buildGoodsByQuery($query, $limit = 10)
    {
        $qb = $this->dbalQueryBuilder();
        $expr = $qb->expr();
        $qb->select('*')->where($expr->andX(
                $expr->eq('t.cat', 0),
                $expr->eq('t.hidden', 0),
                $expr->lte('t.protected', ':permission'),
                $expr->eq('t.deleted', 0),
                $expr->orX(
                    $expr->like('t.name', ':name'),
                    $expr->like('t.text', ':text')
                )
            ))->setMaxResults($limit);

        $qb
            ->setParameter(':permission', $this->app()->getAuth()->getPermission())
            ->setParameter(':name', '%' . $query . '%')
            ->setParameter(':text', '%' . $query . '%')
        ;

        return $qb;
    }


    /**
     * Количество подразделов/товаров по родителю
     * Если type = 1 - то категории
     *      type = 0 - то товары
     *      type = -1 или не задано - категории и товары
     *
     * @param  $parent
     * @param  $type
     *
     * @return string
     */
    public function getCountByParent( $parent, $type = -1 )
    {
        $count = $this->count(
            'parent = :parent AND deleted = 0 AND hidden = 0 ' .
            ( $type != - 1 ? ' AND cat = :type' : '' ),
            array(
                ':parent'=> $parent,
                ':type'  => $type
            )
        );
        return $count;
    }

    /**
     * Найдет путь для страницы
     * Нужна, чтобы строить кэш хлебных крошек при сохранении
     * @param int $id
     *
     * @return string
     */
    public function createSerializedPath( $id )
    {
        $path = array();
        while( $id ) {
            /** @var $obj Catalog */
            $obj = $this->find( $id );
            if( $obj ) {
                $path[ ] = array(
                    'id'  => $obj->id,
                    'name'=> $obj->name,
                );
                $id = $obj[ 'parent' ];
            } else {
                $id = false;
            }
        }
        $path = array_reverse( $path );
        return serialize( $path );
    }

    /**
     * @param $id
     *
     * @return boolean
     */
    public function onDeleteStart( $id = null )
    {
        $this->remove( $id );
        return false;
    }

    /**
     * Удалить в базе
     * @param $id
     *
     * @return void
     */
    public function remove( $id )
    {
        $obj = $this->find( $id );
        if( $obj ) {
            $obj->set( 'deleted', 1 );
            $this->save( $obj );
        }
    }

    /**
     * Восстановить в базе
     * @param $id
     *
     * @return void
     */
    public function unremove( $id )
    {
        $obj = $this->find( $id );
        if( $obj ) {
            $obj->set( 'deleted', 0 );
        }
    }

    /**
     * Creating tree $this->tree by data from $this->all
     * @return array
     */
    public function createTree()
    {
        if (null === $this->parents) {
            $this->parents = array();
            if (!$this->all) {
                $this->all = $this->findAll('`deleted` = 0 AND `cat` = 1', array(), 'pos DESC');
            }
            // create array, indexed by parent field
            foreach ($this->all as $obj) {
                $this->parents[$obj->parent][$obj->id] = $obj;
            }
        }
        return $this->parents;
    }

    /**
     * Вернет список id активных разделов
     *
     * @param $cur_id
     *
     * @return array
     */
    private function createActivePath( $cur_id )
    {
        if( ! $cur_id ) {
            return array();
        }
        $current = $this->find( $cur_id );

        if( 0 == $current->get( 'cat' ) ) {
            $cur_id = $current->get( 'parent' );
        }

        $result = array();

        if( count( $this->parents ) == 0 ) {
            $this->createTree();
        }

        $result[ ] = $cur_id;

        //        $cur_id = $this->getActiveCategory();

        foreach( $this->all as $key => $obj ) {

            //            print "key:{$key}; cur_id:{$cur_id}; obj_id:{$obj->id}; parent:{$obj->parent}<br>";
            // Добавляем для раздела
            if( $cur_id == $obj->id && 0 != $obj->parent ) {
                $active_path = $this->createActivePath( $obj->parent );
                if( $active_path ) {
                    $result = array_merge( $result, $active_path );
                }
            }
            //
        }
        return $result;
    }

    /**
     * Выдаст HTML для выбора раздела в select
     *
     * @param $parent
     * @param $levelback
     *
     * @return array|boolean
     */
    public function getSelectTree($parent, $levelback)
    {
        static $maxlevelback;
        if( $maxlevelback < $levelback ) {
            $maxlevelback = $levelback;
        }

        $list = array();

        if (!$this->all) {
            $this->createTree();
        }

        if ($levelback <= 0) {
            return false;
        }

        if (!isset($this->parents[$parent])) {
            return false;
        }

        /**
         * @var Catalog $branch
         * @var Catalog $obj
         */
        foreach ($this->parents[$parent] as $branch) {
            if (0 == $branch->cat || 1 == $branch->deleted) {
                continue;
            }
            $list[$branch->id] = str_repeat('&nbsp;', 4 * ($maxlevelback - $levelback)) . $branch->name;
            $sublist           = $this->getSelectTree($branch->id, $levelback - 1);
            if ($sublist) {
                foreach ($sublist as $i => $item) {
                    $obj = $this->find($i);
                    if (null === $obj) {
                        continue;
                    }
                    if (1 === $obj->deleted) {
                        continue;
                    }
                    $list[$i] = $item;
                }
            }
        }

        //        $this->log( $list );
        return $list;
    }

    /**
     * Переместить товары в нужный раздел
     * @param $list
     * @param $target
     * @return string
     */
    public function moveList($list, $target)
    {
        /** @var Catalog $item */
        // TODO Не происходит пересчета порядка позиций
        if ($target !== "" && is_numeric($target) && $list && is_array($list)) {
            foreach ($list as $item_id) {
                $item         = $this->find($item_id);
                $item->parent = $target;
                $item->path   = '';
                $this->save($item);
            }
        }
        return 'Successfully';
    }

    /**
     * Массив с категориями для select
     * @return array
     */
    public function getCategoryList()
    {
        $parents = array('Корневой раздел');
        $selectTree = $this->getSelectTree(0, 10);
        $this->log($selectTree, 'select tree');
        if ($selectTree) {
            foreach ($selectTree as $i => $item) {
                $parents[$i] = $item;
            }
        }

        return $parents;
    }

    /**
     * Вернет HTML для лампочки в меню админки
     *
     * @param $id
     * @param $hidden
     *
     * @return string
     */
    public function getOrderHidden( $id, $hidden )
    {
        $return = "<a href='" . $this->app()->getRouter()->createServiceLink(
            'catalog', 'hidden', array( 'id'=> $id )
        ) . "' class='order_hidden'>";
        $return .= $hidden ? Sfcms::html()->icon( 'lightbulb_off', 'Выключен' ) : Sfcms::html()->icon( 'lightbulb', 'Включен' );
        $return .= "</a>";
        return $return;
    }


    /**
     * @param Request $request
     * @return Provider
     */
    public function getProvider($request)
    {
        $provider = new Provider($request, $this->app()->get('siteforever_cms.pager'));
        $provider->setModel($this);

        $criteria = $this->createCriteria();
        $criteria->condition = '`cat` = 0 AND `deleted` = 0';

        $provider->setCriteria($criteria);

        $categories = $this->getCategoryList();

        /** @var $typeModel  */
        $typeModel     = $this->getModel( 'ProductType' );
        $types         = $typeModel->findAll( array('order'=>'name') );

        $manufModel    = $this->getModel( 'Manufacturers' );
        $manufacturers = $manufModel->findAll( array( 'order'=> 'name' ) );

        $provider->setFields(array(
            'id'    => array(
                'title' => 'Id',
                'width' => 50,
                'search' => true,
            ),
            'image' => array(
                'width' => 80,
                'sortable' => false,
                'with' => 'Gallery',
                'format' => array(
                    'image' => array('width'=>50,'height'=>50),
                ),
            ),
            'name'  => array(
                'title' => $this->t('catalog','Name'),
                'width' => 200,
                'format' => array(
                    'link' => array('class'=>'edit', 'controller'=>'catalog', 'action'=>'trade','edit'=>':id','title'=>$this->t('Edit').' :name'),
                ),
                'search' => true,
            ),
            'parent'  => array(
                'title' => $this->t('catalog','Category'),
                'value' => 'Category.title',
                'format' => array(
                    'link' => array('class'=>'edit', 'controller'=>'catalog', 'action'=>'category','edit'=>':parent','title'=>$this->t('Edit').' :name'),
                ),
                'search' => array(
                    'value' => array_map(function($name,$id){
                        return $id.':'.$name;
                    },$categories,array_keys($categories)),
                    'sopt' => 'eq',
                ),
            ),
            'type_id' => array(
                'value' => 'Type.name',
                'title' => $this->t('catalog','Type'),
                'search' => array(
                    'value' => array_merge(array("0:Все типы"),array_map(function($type){
                        return $type->id . ':' . $type->name;
                    },iterator_to_array($types))),
                    'sopt' => 'eq',
                ),
            ),
            'manufacturer' => array(
                'value'=>'Manufacturer.name',
                'title' => $this->t('catalog','Manufacturer'),
                'search' => array(
                    'value' => array_merge(array("0:Все производители"),array_map(function($manuf){
                        return $manuf->id . ':' . $manuf->name;
                    },iterator_to_array($manufacturers))),
                    'sopt' => 'eq',
                ),
            ),
            'articul'   => array(
                'title' => $this->t('catalog','Article'),
                'search' => true,
            ),
            'price1' => array(
                'value'=>'price',
                'title'=> $this->t('catalog','Price'),
                'search' => true,
            ),
            'sale' => array(
                'value' => 'sale',
                'title'=> $this->t('catalog','Sale'),
            ),
            'sale_stop' => array(
                'value' => 'sale_stop',
                'format' => array(
                    'timestamp' => array('format'=>'%Y-%m-%d'),
                ),
                'title'=> $this->t('catalog','Stop'),
            ),
            'hidden' => array(
                'title' => $this->t('catalog','Hidden'),
                'width' => 50,
                'format' => array(
                    'bool' => array('yes'=>Sfcms::html()->icon('lightbulb_off'),'no'=>Sfcms::html()->icon('lightbulb')),
                ),
            ),
            'protected' => array(
                'title' => $this->t('catalog','Protected'),
                'width' => 50,
                'format' => array(
                    'bool' => array('yes'=>Sfcms::html()->icon('lock'),'no'=>Sfcms::html()->icon('lock_break')),
                ),
            ),
            'novelty' => array(
                'title' => $this->t('catalog','Novelty'),
                'width' => 50,
                'format' => array(
                    'bool' => array('yes'=>Sfcms::html()->icon('new')),
                ),
            ),
            'top' => array(
                'title' => $this->t('catalog','To main'),
                'width' => 50,
                'format' => array(
                    'bool' => array(),
                ),
            ),
        ));

        return $provider;
    }
}
