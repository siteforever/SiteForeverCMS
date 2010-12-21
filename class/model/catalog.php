<?php
/**
 * Модель каталога
 * @author KelTanas
 */
class model_Catalog extends Model
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
     * @var form_Form
     */
    protected $form;

    function createTables()
    {
        if ( ! $this->isExistTable( DBCATALOG ) )
        {
            $this->db->query("CREATE TABLE `".DBCATALOG."` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `parent` int(10) unsigned NOT NULL DEFAULT '0',
              `cat` tinyint(1) NOT NULL DEFAULT '0',
              `name` varchar(100) NOT NULL DEFAULT '',
              `url` varchar(100) NOT NULL DEFAULT '',
              `path` text NOT NULL,
              `icon` varchar(250) NOT NULL DEFAULT '',
              `text` text NOT NULL,
              `articul` varchar(250) NOT NULL DEFAULT '',
              `price1` decimal(13,2) NOT NULL DEFAULT '0.00',
              `price2` decimal(13,2) NOT NULL DEFAULT '0.00',
              `pos` int(11) NOT NULL DEFAULT '0',
              `p0` varchar(250) DEFAULT NULL,
              `p1` varchar(250) DEFAULT NULL,
              `p2` varchar(250) DEFAULT NULL,
              `p3` varchar(250) DEFAULT NULL,
              `p4` varchar(250) DEFAULT NULL,
              `p5` varchar(250) DEFAULT NULL,
              `p6` varchar(250) DEFAULT NULL,
              `p7` varchar(250) DEFAULT NULL,
              `p8` varchar(250) DEFAULT NULL,
              `p9` varchar(250) DEFAULT NULL,
              `sort_view` tinyint(1) NOT NULL DEFAULT '1',
              `top` tinyint(1) NOT NULL DEFAULT '0',
              `byorder` tinyint(1) NOT NULL DEFAULT '0',
              `absent` tinyint(1) NOT NULL DEFAULT '0',
              `hidden` tinyint(4) NOT NULL DEFAULT '0',
              `protected` tinyint(4) NOT NULL DEFAULT '0',
              `deleted` tinyint(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        }
        if ( ! $this->isExistTable( DBCATGALLERY ) ) {
            $this->db->query(
                "CREATE TABLE `".DBCATGALLERY."` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `cat_id` int(11) NOT NULL DEFAULT '0',
                      `title` varchar(250) DEFAULT NULL,
                      `descr` varchar(250) DEFAULT NULL,
                      `image` varchar(250) DEFAULT NULL,
                      `middle` varchar(250) DEFAULT NULL,
                      `thumb` varchar(250) DEFAULT NULL,
                      `hidden` tinyint(4) NOT NULL DEFAULT '0',
                      `main` tinyint(4) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`id`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
            );
        }

    }

    /**
     * Поиск по id
     * @param int $id
     * @return array
     */
    function find( $id )
    {
        //printVar($this->data);
        if ( ! isset($this->data['id']) || ( isset($this->data['id']) && $this->data['id'] != $id ) ) {
            $data = $this->db->fetch(
                "SELECT cat.*, cg.image, cg.middle, cg.thumb
                FROM ".DBCATALOG." cat
                LEFT JOIN ".DBCATGALLERY." cg ON cg.cat_id = cat.id
                                         AND cg.hidden = 0
                                         AND cg.main = 1
                WHERE cat.id = '{$id}' AND cat.deleted = 0 LIMIT 1");
            if ( $data ) {
                $this->data = $data;
            }
            else {
                return false;
            }
        }
        return $this->data;
    }

    /**
     * Искать все в список
     * @return array
     */
    function findAll($cond = null)
    {
        $where = '';
        if ( ! is_null( $cond ) ) {
            $where = ' AND '.$cond;
        }

        $data_all = $this->db->fetchAll(
            "SELECT * FROM ".DBCATALOG.
                    " WHERE deleted = 0 ".$where.
            " ORDER BY cat DESC, pos DESC", true
        );
        $this->all = $data_all;
        return $data_all;
    }


    /**
     * Искать все в список по фильтру по артикулу
     * @param string $filter
     * @return array
     */
    function findAllFiltered($filter)
    {
        $data_all = $this->db->fetchAll(
            "SELECT * FROM ".DBCATALOG.
                    " WHERE deleted = 0 AND articul LIKE '%{$filter}%'
            ORDER BY cat DESC, pos DESC", true
        );
        $this->all = $data_all;
        return $data_all;
    }

    /**
     * Искать список по родителю
     * @param int $parent
     * @param string $limit
     * @return array
     */
    function findAllByParent( $parent, $limit = '' )
    {
        $list = App::$db->fetchAll(
            "SELECT cat.*, COUNT(child.id) child_count
            FROM ".DBCATALOG." cat
                LEFT JOIN ".DBCATALOG." child ON child.parent = cat.id AND child.deleted = 0
            WHERE cat.parent = '$parent' AND cat.deleted = 0
            GROUP BY cat.id
            ORDER BY  cat.cat DESC, cat.pos DESC
            {$limit}"
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

        $list = $this->db->fetchAll(
            "SELECT cat.*, cg.image, cg.middle, cg.thumb
            FROM ".DBCATALOG." cat
            LEFT JOIN ".DBCATGALLERY." cg ON cg.cat_id = cat.id
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
            FROM ".DBCATALOG." cat
                LEFT JOIN ".DBCATALOG." sub ON sub.parent = cat.id
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
        $list = $this->db->fetchAll(
            "SELECT cat.*, cg.image, cg.middle, cg.thumb
             FROM ".DBCATALOG." cat
             LEFT JOIN ".DBCATGALLERY." cg ON cg.cat_id = cat.id
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
        $count = App::$db->fetchOne(
            "SELECT COUNT(id)
            FROM ".DBCATALOG."
            WHERE
                parent = '{$parent}' ".
                    ($type != -1 ? " AND cat = {$type} " : "").
                    "AND deleted = 0 AND hidden = 0");
        return $count;
    }

    /**
     * Обновить информацию
     * @return int идентификатор записи
     */
    function update()
    {
        $data = $this->data;
        if ( $data['id'] ) {
            if ( $data['path'] ) {
                $path = json_decode( $this->data['path'] );
            }

            if ( empty($data['path']) || ( $path && $path[0]->name != $data['name'] ) )
            {
                $data['path'] = $this->findPathJSON( $data['id'] );
            }
        }
        unset($data['image']);
        unset($data['middle']);
        unset($data['thumb']);

        $ret = $this->db->insertUpdate( DBCATALOG, $data );

        if ( $ret ) {
            if ( empty( $data['id'] ) ) {
                $data['id'] = $ret;
                $this->data = $data;
                $this->update();
            }
        }
        return $ret;
    }

    /**
     * Найдет путь для страницы
     * Нужна, чтобы строить кэш хлебных крошек при сохранении
     * @param int $id
     * @return string JSON
     */
    function findPathJSON( $id )
    {
        $path = array();
        while( $id ) {
            if ( $this->data['id'] == $id ) {
                $data = $this->data;
            } else {
                $data = $this->db->fetch("SELECT * FROM ".DBCATALOG." WHERE id = $id LIMIT 1");
            }
            if ( $data ) {
                $path[] = array( 'id'=>$data['id'], 'name'=>$data['name'], 'url'=>$data['url']);
                $id = $data['parent'];
            }
            else {
                $id = 0;
            }
        }
        $path = array_reverse( $path );
        return json_encode( $path );
    }

    /**
     * Удалить в базе
     * @return void
     */
    function delete()
    {
        if ( isset( $this->data['id'] ) ) {
            $this->data['deleted'] = 1;
            $this->all[$this->data['id']] = $this->data;
            $this->update();
        }
    }

    /**
     * Восстановить в базе
     * @return void
     */
    function undelete()
    {
        if ( isset( $this->data['id'] ) ) {
            $this->data['deleted'] = 0;
            $this->all[$this->data['id']] = $this->data;
            $this->update();
        }
    }

    /**
     * Создает дерево $this->tree по данным из $this->all
     * @param $parent
     */
    function createTree( $parent = 0 )
    {
        $this->parents = array();
        $this->findAll('cat = 1');
        // создаем массив, индексируемый по родителям
        foreach( $this->all as &$data ) {
            $this->parents[ $data['parent'] ][ $data['id'] ] =& $data;
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

        //$cur_id = $this->request->get( 'cat', FILTER_VALIDATE_INT );
        $cur_id = $this->request->get( 'cat', FILTER_SANITIZE_NUMBER_INT );
        //print $cur_id;

        //print $cur_id;

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
                $this->db->insertUpdateMulti(DBCATALOG, $data);
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
            if ( $target !== false && is_numeric($target) && $list && is_array( $list ) ) {
                //printVar($list);
                foreach( $list as $item_id ) {
                    $this->find($item_id);
                    $this->set( 'parent', $target );
                    $this->set( 'path', '' ); // форсируем пересчет пути
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

        function getForm()
        {
            if ( is_null( $this->form ) ) {

                //$parents[0] = 'Корневой раздел';
                //printVar( $parents );
                $parents    = $this->getCategoryList();

                $this->form = new form_Form(array(
                    'name'  => 'catalog',
                    'title' => 'Раздел каталога',
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

                        'url'       => array('type'=>'hidden', 'label'=>'Адрес',),
                        'path'      => array('type'=>'hidden'),

                        /*'image'     => array(
                            'type'  => 'text',
                            'label' =>'Изображение',
                            'hidden',
                        ),*/
                        'icon'      => array(
                            'type'  => 'select',
                            'label' => 'Иконка',
                            'value' => '',
                            'variants'  => array('Нет изображений'),
                        ),

                        'articul'   => array('type'=>'text', 'label'=>'Артикул', 'value'=>'', 'hidden'),
                        'price1'    => array('type'=>'text', 'label'=>'Цена роз.', 'value'=>'0', 'hidden'),
                        'price2'    => array('type'=>'text', 'label'=>'Цена опт.', 'value'=>'0', 'hidden'),
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
                            'variants'  => $this->config->get('users.groups'),
                        ),
                        'sep'       => array('type'=>'separator'),
                        'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
                    ),
                ));
            }

            return $this->form;
        }



    }





