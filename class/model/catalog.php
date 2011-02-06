<?php
/**
 * Модель каталога
 * @author KelTanas
 */
class Model_Catalog extends Model
{
    /**
     * Массив, индексируемый по $parent
     * @var array
     */
    protected $parents;

    /**
     * Списков разделов в кэше
     * @var array
     */
    protected $all = array();

    public $html = array();

    /**
     * @var Forms_Catalog_Edit
     */
    protected   $form;

    /**
     * @return Model_CatGallery
     */
    function gallery()
    {
        return self::getModel('CatGallery');
    }

    /**
     * Инициализация
     * @return void
     */
    protected function Init()
    {
        $this->request->addScript( $this->request->get('path.misc') . '/etc/catalog.js' );
    }

    /**
     * Поиск по id
     * @param int $id
     * @return array
     */
    /*function find( $id )
    {
        $data = parent::find(array(
            'cond'  => 'id = :id AND deleted = 0',
            'params'=> array(':id'=>$id),
         ));

        if ( ! $data ) {
            return null;
        }

        $image  = $this->gallery()->find(array(
            'cond'      => 'cat_id = :id AND hidden = 0 AND main = 1',
            'params'    => array(':id'=>$id),
           ));

        if ( $image ) {
            $data['image']  = $image['image'];
            $data['middle'] = $image['middle'];
            $data['thumb']  = $image['thumb'];
        }
        $this->setData( $data ); // для совместимости
        return $data;
    }*/

    /**
     * Искать все в список по фильтру по артикулу
     * @param string $filter
     * @return array
     */
    function findAllFiltered($filter)
    {
        return $this->findAll(array(
            'cond'      => "deleted = 0 AND articul LIKE :filter",
            'params'    => array(':filter'=>'%'.$filter.'%'),
            'order'     => 'cat DECS, pos DESC'
        ), true);
    }

    /**
     * Искать список по родителю
     * @param int $parent
     * @param string $limit
     * @return array
     */
    function findAllByParent( $parent, $limit = 'LIMIT 100' )
    {
        $list = $this->db->fetchAll(
            "SELECT cat.*, COUNT(child.id) child_count
            FROM {$this->getTable()} cat
                LEFT JOIN {$this->getTable()} child ON child.parent = cat.id AND child.deleted = 0
            WHERE cat.parent = :parent AND cat.deleted = 0
            GROUP BY cat.id
            ORDER BY  cat.cat DESC, cat.pos DESC
            {$limit}",
            true, db::F_ASSOC, array(':parent'=>$parent)
        );
        return $list;
    }

    /**
     * Искать продукты по родителю
     * @param  $parent
     * @param string $limit
     * @return array
     */
    function findGoodsByParent( $parent, $limit = '' )
    {
        $order = $this->config->get('catalog.order_default');

        // Примеряем способ сортировки к списку из конфига
        $order_list = $this->config->get('catalog.order_list');


        if ( $order_list && is_array($order_list) ) {
            $set = $this->request->get('order');
            if ( $set && $this->config->get('catalog.order_list.'.$set) ) {
                $order = $set;
            }
            else {
                $order  = reset( array_keys($order_list) );
            }
            $this->request->set('order', $order);
            //print $order;
        }

        //print "order=$order";
        $gallery_table  = $this->gallery()->getTable();

        $list = $this->db->fetchAll(
            "SELECT cat.*, cg.image, cg.middle, cg.thumb
            FROM {$this->getTable()} cat
            LEFT JOIN {$gallery_table} cg ON cg.cat_id = cat.id
                                         AND cg.hidden = 0
                                         AND cg.main = 1
            WHERE cat.parent = '$parent'
                AND cat.cat = 0
                AND cat.deleted = 0
                AND cat.hidden = 0 ".
            ($order ? " ORDER BY {$order}" : "").
            "{$limit}"
        );
        return $list;
    }

    /**
     * Искать категории по родителю
     * @param  $parent
     * @param string $limit
     * @return array
     */
    function findCatsByParent( $parent, $limit = '' )
    {
        $list = $this->db->fetchAll(
            "SELECT cat.*, COUNT(sub.id) sub_count
            FROM {$this->getTable()} cat
                LEFT JOIN {$this->getTable()} sub ON sub.parent = cat.id
                    AND sub.cat = 0
                    AND sub.deleted = 0
                    AND sub.hidden = 0
            WHERE cat.parent = '$parent'
                AND cat.cat = 1
                AND cat.deleted = 0
                AND cat.hidden = 0
            GROUP BY cat.id
            {$limit}"
        );
        return $list;
    }

    /**
     * Поиск списка товаров из списка id
     * @throws Exception
     * @param array $id_list
     * @return array
     */
    function findGoodsById( $id_list )
    {
        if ( ! is_array( $id_list ) ) {
            throw new Exception('Аргумент должен быть массивом');
        }

        $gallery_table  = $this->gallery()->getTable();

        $list = $this->db->fetchAll(
            "SELECT cat.*, cg.image, cg.middle, cg.thumb
             FROM {$this->getTable()} cat
             LEFT JOIN {$gallery_table} cg ON cg.cat_id = cat.id
                                         AND cg.hidden = 0
                                         AND cg.main = 1
             WHERE cat.id IN (".join(',', $id_list).")",
            true);
        return $list;
    }

    /**
     * Количество подразделов/товаров по родителю
     * Если type = 1 - то категории
     *      type = 0 - то товары
     *      type = -1 или не задано - категории и товары
     * @param  $parent
     * @param  $type
     * @return string
     */
    function getCountByParent( $parent, $type = -1 )
    {
        $count  = $this->count(
            'parent = :parent AND deleted = 0 AND hidden = 0 '.
                ($type != -1 ? ' AND cat = :type' : ''),
            array(':parent'=>$parent, ':type'=>$type)
        );
        /*$count = App::$db->fetchOne(
            "SELECT COUNT(id)
            FROM {$this->getTable()}
            WHERE
                parent = '{$parent}' ".
                    ($type != -1 ? " AND cat = {$type} " : "").
                    "AND deleted = 0 AND hidden = 0");*/
        return $count;
    }

    /**
     * Обновить информацию
     * @return int идентификатор записи
     */
    function update( Data_Object_Catalog $obj )
    {
        if ( $obj['id'] ) {
            if ( $obj['path'] ) {
                $path = serialize( $obj['path'] );
            }

            if ( ! $obj->path || ( $path && $path[0]->name != $obj['name'] ) )
            {
                $obj['path'] = $this->findPathSerialize( $obj['id'] );
            }
        }
        unset($obj['image']);
        unset($obj['middle']);
        unset($obj['thumb']);

        $ret = $this->db->insertUpdate( $this->getTable(), $obj->getAttributes() );

        if ( $ret ) {
            if ( empty( $obj['id'] ) ) {
                $obj['id'] = $ret;
                $this->update( $obj );
            }
        }
        return $ret;
    }

    /**
     * Найдет путь для страницы
     * Нужна, чтобы строить кэш хлебных крошек при сохранении
     * @param int $id
     * @return string
     */
    function findPathSerialize( $id )
    {
        $path = array();
        while( $id ) {
            $obj    = $this->find($id);
            if ( $obj ) {
                $path[] = array( 'id'=>$obj['id'], 'name'=>$obj['name'], 'url'=>$obj['url']);
                $id = $obj['parent'];
            }
            else {
                $id = 0;
            }
        }
        $path = array_reverse( $path );
        return serialize( $path );
    }

    /**
     * Удалить в базе
     * @return void
     */
    function remove( $id )
    {
        $obj    = $this->find( $id );
        if ( $obj ) {
            $obj->deleted   = 1;
        }
    }

    /**
     * Восстановить в базе
     * @return void
     */
    function unremove( $id )
    {
        $obj    = $this->find( $id );
        if ( $obj ) {
            $obj->deleted   = 0;
        }
    }

    /**
     * Создает дерево $this->tree по данным из $this->all
     * @param $parent
     */
    function createTree( $parent = 0 )
    {
        $this->parents = array();
        if ( count( $this->all ) == 0 ) {
            $this->all = $this->findAll(array('cond'=>'cat = 1'));
        }
        // создаем массив, индексируемый по родителям
        foreach( $this->all as $obj ) {
            $this->parents[ $obj['parent'] ][ $obj['id'] ] = $obj;
        }
    }

    /**
     * Вернет меню для раздела каталога
     * @param string $url
     * @param int $parent
     * @param int $level
     */
    function getMenu( $url, $parent, $levelback, $chain = array() )
    {
        $html = "";

        $cur_id = $this->request->get( 'cat', FILTER_SANITIZE_NUMBER_INT );

        if ( count($this->parents) == 0 ) {
            $this->createTree();
            //printVar($this->all);
        }

        if ( $levelback <= 0 ) {
            return '';
        }

        if ( ! isset($this->parents[ $parent ]) ) {
            return '';
        }

        if ( count( $chain ) == 0 ) {
            //$chain[]    = $cur_id;
            if ( $this->data['id'] == $cur_id ) {
                $step_id = $cur_id;
            } else {
                $step_id = $this->data['id'];
            }
            //$step_id = $this->data['id'];
            while( isset( $this->all[ $step_id ] ) ) {
                $chain[] = $step_id;
                $step_id = $this->all[ $step_id ]['parent'];
            }
        }

        //printVar($chain);

        $html .= "<ul>";
        foreach( $this->parents[ $parent ] as $branch )
        {
            //print $branch['id'];
            $active = in_array( $branch['id'], $chain ) ? ' active' : '';

            //printVar($cur_cat['parent']);
            //printVar($chain);

            if ( $branch['icon'] ) {
                $html .= "<li class='cat-{$branch['id']}{$active}' style='background:url(/".$branch['icon'].") no-repeat 6px 4px;'>";
            } else {
                $html .= "<li class='cat-{$branch['id']}{$active}'>";
            }

            if ( $branch['id'] == $cur_id )
            {
                //$html .= $branch['name'];
                $active = true;
            }
            //else {
            $html .= "<a ".href($url, array('cat'=>$branch['id'])).($active?" class='active'":'').">{$branch['name']}</a>";
            //}

            $html .= $this->getMenu( $url, $branch['id'], $levelback - 1, $chain );
            $html .= "</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    /**
     * Выдаст HTML для выбора раздела в select
     * @return array
     */
    function getSelectTree( $parent, $levelback )
    {
        static $maxlevelback;
        if ( $maxlevelback < $levelback ) {
            $maxlevelback = $levelback;
        }

        $list   = array();

        if ( count($this->parents) == 0 ) {
            $this->createTree();
        }

        if ( $levelback <= 0 ) {
            return false;
        }

        if ( ! isset($this->parents[ $parent ]) ) {
            return false;
        }


        foreach( $this->parents[ $parent ] as $branch ) {

            if ( ! $branch['cat'] ) {
                continue;
            }

            $list[ $branch['id'] ] = str_repeat('&nbsp;', 8 * ( $maxlevelback - $levelback )).$branch['name'];
            $sublist    = $this->getSelectTree( $branch['id'], $levelback-1 );
            if ( $sublist ) {
                foreach( $sublist as $i => $item ) {
                    $list[$i]   = $item;
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
        $sort = $this->request->get('sort');
        if ( $sort && is_array( $sort ) ) {
            $data = array();
            foreach( $sort as $pos => $item_id ) {
                $data[] = array('id'=>$item_id, 'pos'=>$pos);
            }
            $this->db->insertUpdateMulti($this->getTable(), $data);
        } else {
            return t('Error in sorting params');
        }
        return '';
    }

    /**
     * Переместить товары в нужный раздел
     * @return mixed
     */
    function moveList()
    {
        $list   = $this->request->get('move_list');
        $target = $this->request->get('target', FILTER_SANITIZE_NUMBER_INT);
        // TODO Не происходит пересчета порядка позиций

        if ( $target !== "" && is_numeric($target) && $list && is_array( $list ) ) {
            //printVar($list);
            foreach( $list as $item_id ) {
                $item   = $this->find($item_id);
                $item->parent   = $target;
                $item->path     = '';
                $this->update();
            }
        }
        return '';
    }

    /**
     * Массив с категориями для select
     * @return array
     */
    function &getCategoryList()
    {
        $parents = array('Корневой раздел');
        $select_tree    = $this->getSelectTree(0,10);
        if ( $select_tree ) {
            foreach( $select_tree as $i => $item ) {
                $parents[$i] = $item;
            }
        }
        return $parents;
    }

    /**
     * @return Forms_Catalog_Edit
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_Catalog_Edit();
        }
        return $this->form;
    }


    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_Catalog';
    }

    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_Catalog';
    }
}





