<?php
/**
 * Модель каталога
 * @author KelTanas
 */
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
    protected $all = array();

    public $html = array();

    /**
     * @var Forms_Catalog_Edit
     */
    protected $form;

    /**
     * @return Model_CatGallery
     */
    function gallery()
    {
        return self::getModel( 'CatGallery' );
    }

    /**
     * Инициализация
     * @return void
     */
    protected function Init()
    {
        $this->request->addScript( $this->request->get( 'path.misc' ) . '/etc/catalog.js' );
        $this->request->addStyle( $this->request->get( 'path.misc' ) . '/etc/catalog.css' );
    }

    /**
     * Искать все в список по фильтру по артикулу
     * @param string $filter
     *
     * @return array
     */
    function findAllFiltered( $filter )
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
    function findAllByParent( $parent, $limit = 'LIMIT 100' )
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
    function findGoodsByParent( $parent, $limit = '' )
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
    function findCatsByParent( $parent, $limit = '' )
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
    function findGoodsById( $id_list )
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
    function getCountByParent( $parent, $type = -1 )
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
     * Обновить информацию
     * @param Data_Object_Catalog $obj
     *
     * @return int идентификатор записи
     */
    function update( Data_Object_Catalog $obj )
    {
        $obj_id = $obj->getId();

        $path = null;
        if( $obj_id ) {
            if( $obj->get( 'path' ) ) {
                $path = unserialize( $obj->get( 'path' ) );
            }
            //printVar( $path );
            //printVar( $obj->getAttributes() );

            if( ! $obj->get( 'path' ) || ( $path && is_array( $path ) && $path[ 0 ][ 'name' ] != $obj->get( 'name' ) )
            ) {
                $obj->set( 'path', $this->findPathSerialize( $obj->getId() ) );
            }
        }
        //unset($obj['image']);
        //unset($obj['middle']);
        //unset($obj['thumb']);

        $ret = $this->save( $obj );

        // Если мы не знали своего ID (новый), то надо пересоздать путь и сохранить снова
        if( ! $obj_id ) {
            $this->update( $obj );
        }
        return $ret;
    }

    /**
     * Найдет путь для страницы
     * Нужна, чтобы строить кэш хлебных крошек при сохранении
     * @param int $id
     *
     * @return string
     */
    function findPathSerialize( $id )
    {
        $path = array();
        while( $id ) {
            $obj = $this->find( $id );
            if( $obj ) {

                $path[ ] = array(
                    'id'  => $obj[ 'id' ],
                    'name'=> $obj[ 'name' ],
                    'url' => $obj[ 'url' ]
                );

                $id = $obj[ 'parent' ];
            }
            else {
                $id = 0;
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
    function onDeleteStart( $id = null )
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
    function remove( $id )
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
    function unremove( $id )
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
    function createTree()
    {
        if( null === $this->parents ) {
            $this->parents = array();
            if( count( $this->all ) == 0 ) {
                $this->all = $this->findAll( 'cat = 1 AND deleted = 0', array(), 'pos DESC' );
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
    function getMenu( $url, $parent, $levelback = 0 )
    {
        $cur_id = $this->getActiveCategory();

        $path = $this->createActivePath( $cur_id );
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
            $active = in_array( $branch[ 'id' ], $path ) || $branch[ 'id' ] == $cur_id
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
        return implode( '', $html );
    }

    /**
     * Выдаст HTML для выбора раздела в select
     *
     * @param $parent
     * @param $levelback
     *
     * @return array|boolean
     */
    function getSelectTree( $parent, $levelback )
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

//            var_dump( get_class( $branch ) . $branch->id );

            if( 0 == $branch->cat || 1 == $branch->deleted ) {
                continue;
            }

            $list[ $branch->id ] = str_repeat( '&nbsp;', 8 * ( $maxlevelback - $levelback ) ) . $branch->name;
            $sublist             = $this->getSelectTree( $branch->id, $levelback - 1 );
            if( $sublist ) {
                foreach( $sublist as $i => $item ) {
                    $obj    = $this->all->getRow( $i );
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

        return $list;
    }

    /**
     * Пересортирует разделы
     * @return string
     */
    function resort()
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
        }
        else {
            return t( 'Error in sorting params' );
        }
        return '';
    }

    /**
     * Переместить товары в нужный раздел
     * @return void
     */
    function moveList()
    {
        /**
         * @var Data_Object_Catalog $item
         */
        $list   = $this->request->get( 'move_list' );
        $target = $this->request->get( 'target', FILTER_SANITIZE_NUMBER_INT );
        // TODO Не происходит пересчета порядка позиций

        if( $target !== "" && is_numeric( $target ) && $list && is_array( $list ) ) {
            //printVar($list);
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
    function &getCategoryList()
    {
        $parents     = array( 'Корневой раздел' );
        $select_tree = $this->getSelectTree( 0, 10 );
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
    function getForm()
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
}
