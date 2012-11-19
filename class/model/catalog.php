<?php
/**
 * Модель каталога
 * @author KelTanas
 */
use Sfcms\JqGrid\Provider;

class Model_Catalog extends Sfcms_Model
{
    /**
     * Массив, индексируемый по $parent
     * @var array
     */
    protected $parents = null;

    /**
     * Списков разделов в кэше
     * @var Data_Collection
     */
    protected $all = null;

    public $html = array();

    /**
     * @var Forms_Catalog_Edit
     */
    protected $form = null;

    /**
     * Инициализация
     * @return void
     */
    protected function Init()
    {
        $this->app()->addScript( $this->request->get( 'path.misc' ) . '/etc/catalog.js' );
        $this->app()->addStyle( $this->request->get( 'path.misc' ) . '/etc/catalog.css' );
    }


    public function relation()
    {
        return array(
            'Gallery'       => array( self::HAS_MANY, 'CatalogGallery', 'cat_id' ),
            'Category'      => array( self::BELONGS, 'Catalog', 'parent' ),
            'Manufacturer'  => array( self::BELONGS, 'Manufacturers', 'manufacturer' ),
            'Material'      => array( self::BELONGS, 'Material', 'material' ),
            'Goods'         => array( self::HAS_MANY, 'Catalog', 'parent' ),
            'Page'          => array( self::HAS_ONE, 'Page', 'link' ),
        );
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
     * @param $parentId
     * @return array
     */
    public function getAllChildrensIds( $parentId, $level = 0 )
    {
        $level--;
        /** @var $child Data_Object_Catalog */
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
     * @return array
     */
    public function findAllByParent( $parent, $limit = 'LIMIT 100' )
    {
        $list = $this->db->fetchAll(
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
     * Искать продукты по родителю
     * @param        $parent
     * @param string $limit
     *
     * @return array
     */
    public function findGoodsByParent( $parent, $limit = '' )
    {
        $order = $this->config->get( 'catalog.order_default' );

        // Примеряем способ сортировки к списку из конфига
        $order_list = $this->config->get( 'catalog.order_list' );


        if( $order_list && is_array( $order_list ) ) {
            $set = $this->request->get( 'order' );
            if( $set && $this->config->get( 'catalog.order_list.' . $set ) ) {
                $order = $set;
            }
            else {
                $order = reset( array_keys( $order_list ) );
            }
            $this->request->set( 'order', $order );
            //print $order;
        }

        //print "order=$order";
        $gallery_table = $this->gallery()->getTable();

        $list = $this->db->fetchAll(
            "SELECT cat.*, cg.image, cg.middle, cg.thumb "
            . "FROM {$this->getTable()} cat "
            . "LEFT JOIN {$gallery_table} cg ON cg.cat_id = cat.id "
            . " AND cg.hidden = 0 "
            . " AND cg.main = 1 "
            . "WHERE cat.parent = '$parent' "
            . "    AND cat.cat = 0 "
            . "    AND cat.deleted = 0 "
            . "    AND cat.hidden = 0 "
            . ( $order ? " ORDER BY {$order}" : "" )
            . "{$limit}"
        );
        return $list;
    }

    /**
     * Искать категории по родителю
     * @param        $parent
     * @param string $limit
     *
     * @return array
     */
    public function findCatsByParent( $parent, $limit = '' )
    {
        $list = $this->db->fetchAll(
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
     * Поиск списка товаров из списка id
     * @throws Exception
     *
     * @param array $id_list
     *
     * @return array
     */
    public function findGoodsById( $id_list )
    {
        if( ! is_array( $id_list ) ) {
            throw new Exception( 'Аргумент должен быть массивом' );
        }

        $gallery_table = $this->gallery()->getTable();

        $list = $this->db->fetchAll(
            "SELECT cat.*, cg.image, cg.middle, cg.thumb "
            . "FROM {$this->getTable()} cat "
            . "LEFT JOIN {$gallery_table} cg ON cg.cat_id = cat.id "
            . "                            AND cg.hidden = 0 "
            . "                            AND cg.main = 1 "
            . "WHERE cat.id IN (" . join( ',', $id_list ) . ")",
            true
        );
        return $list;
    }


    /**
     * Товары, отсортированные по top
     * @param int $limit
     * @return array|Data_Collection
     */
    public function findProductsSortTop( $limit = 4 )
    {
        return $this->with('Gallery')
            ->findAll('deleted = 0 AND hidden = 0 AND protected = 0 AND cat = 0',array(),'top DESC', $limit);
    }



    /**
     * Поиск товаров по ключевой фразе
     * @param $query
     * @return Data_Collection
     */
    public function findGoodsByQuery( $query )
    {
        $list = $this->getDB()->fetchAll(
            "SELECT * FROM {$this->getTable()} "
            . "WHERE `cat` = 0 AND `hidden` = 0 AND `protected` <= ? AND `deleted` = 0 "
                . "AND ( `name` LIKE ? OR `text` LIKE ? ) "
            . "LIMIT 10",
            false,
            PDO::FETCH_ASSOC,
            array(
                $this->app()->getAuth()->currentUser()->getPermission(),
                '%'.$query.'%',
                '%'.$query.'%'
            )
        );
        return new Data_Collection( $list, $this );
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
     * @param Data_Object_Catalog $obj
     * @return bool
     */
    public function onSaveStart( Data_Object $obj = null )
    {
        // If object will update
        /** @var $obj Data_Object_Catalog */
        if( $obj->getId() ) {
            $obj->path = $this->createSerializedPath( $obj->getId() );
        }

        if ( $obj->cat ) {
            // @todo Надо сделать слежение за изменением иерархии
            $objPage = $obj->Page;
            if ( $objPage ) {
                $objPage->name = $obj->name;
                $objPage->hidden = $obj->hidden;
                $objPage->protected = $obj->protected;
                $objPage->markDirty();
            }
        }

        return true;
    }

    /**
     * @param Data_Object $obj
     * @return boolean
     */
    public function onSaveSuccess( Data_Object $obj = null )
    {
        /** @var $obj Data_Object_Catalog */
        // If object was just created
        if ( ! $obj->path ) {
            $obj->path = $this->createSerializedPath( $obj->getId() );
            $this->save( $obj );
        }
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
            /** @var $obj Data_Object_Catalog */
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
     * Создает дерево $this->tree по данным из $this->all
     * @return array
     */
    public function createTree()
    {
        if( null === $this->parents ) {
            $this->parents = array();
            if( count( $this->all ) == 0 ) {
                $this->all = $this->findAll( 'cat = ? AND deleted = ?', array( 1, 0 ), 'pos DESC' );
            }
            // создаем массив, индексируемый по родителям
            foreach( $this->all as $obj ) {
                $this->parents[ $obj->parent ][ $obj->id ] = $obj;
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
     * Определяет id активной категории
     * @return boolean|int|mixed
     */
    private function getActiveCategory()
    {
        $result     = null;
        $id         = $this->request->get( 'id' );
        $cat        = $this->request->get( 'cat' );
        $controller = $this->request->get( 'controller' );

        if( null !== $cat ) {
            $result = $cat;
        } elseif( strpos( $controller, 'catalog' ) && null !== $id ) {
            $result = $id;
        }

//        var_dump($result);
        return $result;
    }

    /**
     * Вернет меню для раздела каталога
     * @param     $url
     * @param     $parent
     * @param int $levelback
     *
     * @return string
     */
    public function getMenu( $url, $parent, $levelback = 0 )
    {
        $curId = $this->getActiveCategory();

        $path = $this->createActivePath( $curId );
        //        printVar($path);

        if( count( $this->parents ) == 0 ) {
            $this->createTree();
        }

        //        printVar($levelback);
        if( $levelback <= 0 ) {
            return '';
        }

        if( ! isset( $this->parents[ $parent ] ) ) {
            return '';
        }

        $build_list = array();
        foreach( $this->parents[ $parent ] as $branch ) {
            if( $branch[ 'hidden' ] ) {
                continue;
            }
            $build_list[ ] = $branch;
        }

        $start   = microtime( 1 );

        $html    = array( '<ul>' );
        $counter = 1;
        foreach( $build_list as $branch )
        {
            $active = in_array( $branch[ 'id' ], $path ) || $branch[ 'id' ] == $curId
                ? ' active'
                : '';

            $last  = count( $build_list ) == $counter ? ' last' : '';
            $first = 1 == $counter ++ ? ' first' : '';

            $html[ ] = "<li class='cat-{$branch['id']}{$active}{$first}{$last}' "
                       . ( $branch[ 'icon' ]
                    ? "style='background:url(/" . $branch[ 'icon' ] . ") no-repeat 6px 4px;'"
                    : "" ) . ">";

            $html[ ] = "<a href='" . $this->app()->getRouter()->createLink(
                $url, array( 'id'=> $branch[ 'id' ] )
            ) . "'"
                       . ( $active
                    ? " class='active'"
                    : '' )
                       . ">{$branch['name']}</a>";

            if( $active ) {
                $html[ ] = $this->getMenu( $url, $branch[ 'id' ], $levelback - 1 );
            }
            $html[ ] = '</li>';
        }
        $html[ ] = '</ul>';
        return implode( "\n", $html );
    }

    /**
     * Выдаст HTML для выбора раздела в select
     *
     * @param $parent
     * @param $levelback
     *
     * @return array|boolean
     */
    public function getSelectTree( $parent, $levelback )
    {
        static $maxlevelback;
        if( $maxlevelback < $levelback ) {
            $maxlevelback = $levelback;
        }

        $list = array();

        if( count( $this->all ) == 0 ) {
            $this->createTree();
        }

        if( $levelback <= 0 ) {
            return false;
        }

        if( ! isset( $this->parents[ $parent ] ) ) {
            return false;
        }

        /**
         * @var Data_Object_Catalog $branch
         * @var Data_Object_Catalog $obj
         */
        foreach( $this->parents[ $parent ] as $branch ) {
            if( 0 == $branch->cat || 1 == $branch->deleted ) {
                continue;
            }
            $list[ $branch->id ] = str_repeat( '&nbsp;', 8 * ( $maxlevelback - $levelback ) ) . $branch->name;
            $sublist             = $this->getSelectTree( $branch->id, $levelback - 1 );
            if( $sublist ) {
                foreach( $sublist as $i => $item ) {
                    $obj    = $this->find( $i );
                    if ( null === $obj ) {
                        continue;
                    }
                    if( 1 === $obj->deleted ) {
                        continue;
                    }
                    $list[ $i ] = $item;
                }
            }
        }
//        $this->log( $list );
        return $list;
    }

    /**
     * Пересортирует разделы
     * @return string
     */
    public function resort()
    {
        $sort = $this->request->get( 'sort' );
        if( $sort && is_array( $sort ) ) {
            $data = array();
            foreach( $sort as $pos => $item_id ) {
                $data[ ] = array(
                    'id' => $item_id,
                    'pos'=> $pos
                );
            }
            $this->db->insertUpdateMulti( $this->getTable(), $data );
        } else {
            return t( 'Error in sorting params' );
        }
        return '';
    }

    /**
     * Переместить товары в нужный раздел
     * @return void
     */
    public function moveList()
    {
        /** @var Data_Object_Catalog $item */
        $list   = $this->request->get( 'move_list' );
        $target = $this->request->get( 'target', FILTER_SANITIZE_NUMBER_INT );
        // TODO Не происходит пересчета порядка позиций

        if( $target !== "" && is_numeric( $target ) && $list && is_array( $list ) ) {
            foreach( $list as $item_id ) {
                $item         = $this->find( $item_id );
                $item->parent = $target;
                $item->path   = '';
                $this->update( $item );
            }
        }
    }

    /**
     * Массив с категориями для select
     * @return array
     */
    public function getCategoryList()
    {
        $parents     = array( 'Корневой раздел' );
        $select_tree = $this->getSelectTree( 0, 10 );
//        $this->log( $select_tree, 'select tree' );
        if( $select_tree ) {
            foreach( $select_tree as $i => $item ) {
                $parents[ $i ] = $item;
            }
        }
        return $parents;
    }

    /**
     * @return Forms_Catalog_Edit
     */
    public function getForm()
    {
        if( is_null( $this->form ) ) {
            $this->form = new Forms_Catalog_Edit();
        }
        return $this->form;
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
        $return .= $hidden ? icon( 'lightbulb_off', 'Выключен' ) : icon( 'lightbulb', 'Включен' );
        $return .= "</a>";
        return $return;
    }


    /**
     * @return Sfcms\JqGrid\Provider
     */
    public function getProvider()
    {
        $provider = new Provider( $this->app() );
        $provider->setModel( $this );

        $criteria = $this->createCriteria();
        $criteria->condition = 'cat = 0 AND deleted = 0';

        $provider->setCriteria( $criteria );

        $provider->setFields(array(
            'id'    => array(
                'title' => 'Id',
                'width' => 50,
            ),
            'image' => array(
                'width' => 80,
                'sortable' => false,
                'format' => array(
                    'image' => array('width'=>50,'height'=>50),
                ),
            ),
            'name'  => array(
                'title' => t('catalog','Name'),
                'width' => 200,
                'format' => array(
                    'link' => array('controller'=>'goods', 'action'=>'edit','id'=>':id'),
                ),
            ),
            'parent'  => array(
                'title' => t('catalog','Category'),
                'value' => 'Category.title',
            ),
            'manufacturer' => array(
                'value'=>'Manufacturer.name',
                'title' => t('catalog','Manufacturer')
            ),
            'articul'   => t('catalog','Articul'),
            'price1' => array(
                'value'=>'price',
                'title'=> t('catalog','Price')
            ),
            'hidden' => array(
                'title' => t('catalog','Hidden'),
                'width' => 50,
                'format' => array(
                    'bool' => array('yes'=>Sfcms::html()->icon('lightbulb_off'),'no'=>Sfcms::html()->icon('lightbulb')),
                ),
            ),
            'protected' => array(
                'title' => t('catalog','Protected'),
                'width' => 50,
                'format' => array(
                    'bool' => array('yes'=>Sfcms::html()->icon('lock'),'no'=>Sfcms::html()->icon('lock_break')),
                ),
            ),
            'novelty' => array(
                'title' => t('catalog','Novelty'),
                'width' => 50,
                'format' => array(
                    'bool' => array('yes'=>Sfcms::html()->icon('new')),
                ),
            ),
            'top' => array(
                'title' => t('catalog','To main'),
                'width' => 50,
                'format' => array(
                    'bool' => array(),
                ),
            ),
        ));

        return $provider;
    }
}
