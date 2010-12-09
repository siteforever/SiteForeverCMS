<?php
/**
 * Модель структуры
 */
class model_Structure extends Model
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
     * Форма редактирования
     * @var form_Form
     */
    private $form;


    protected   $available_modules;


    function createTables()
    {
        if ( ! $this->isExistTable( DBSTRUCTURE ) ) {
            $this->db->query("
            CREATE TABLE `".DBSTRUCTURE."` (
              `id` int(11) NOT NULL auto_increment,
              `parent` int(11) NOT NULL default '0',
              `name` varchar(80) NOT NULL default '',
              `template` varchar(50) NOT NULL default 'inner',
              `uri` varchar(100) NOT NULL default '',
              `alias` varchar(250) NOT NULL default '',
              `path` varchar(250) NOT NULL default '',
              `date` int(11) NOT NULL default '0' COMMENT 'time stamp',
              `update` int(11) NOT NULL default '0' COMMENT 'time stamp',
              `pos` int(11) NOT NULL default '0',
              `link` tinyint(1) NOT NULL default '0' COMMENT '1 - there out link',
              `controller` varchar(20) NOT NULL default 'page',
              `action` varchar(20) NOT NULL default 'index',
              `sort` varchar(20) NOT NULL default 'pos ASC',
              `title` varchar(80) NOT NULL default '',
              `notice` text,
              `content` text,
              `thumb` varchar(250) NOT NULL default '',
              `image` varchar(250) NOT NULL default '',
              `keywords` varchar(120) NOT NULL default '',
              `description` varchar(120) NOT NULL default '',
              `author` int(11) NOT NULL default '0',
              `hidden` tinyint(4) NOT NULL default '0',
              `protected` tinyint(4) NOT NULL default '0',
              `system` tinyint(4) NOT NULL default '0',
              `deleted` tinyint(1) NOT NULL default '0',
              PRIMARY KEY  (`id`),
              KEY `id_structure` (`parent`),
              KEY `url` (`uri`),
              KEY `date` (`date`),
              KEY `order` (`parent`,`pos`),
              KEY `request` (`alias`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0
            ");
        }
    }

    /**
     * Вернет список доступных модулей
     * @return array
     */
    function getAvaibleModules()
    {
        if ( is_null( $this->available_modules ) ) {
            $this->available_modules = array(
                'page'      => array(
                    'label'     => 'Страница',
                ),
                'news'      => array(
                    'label'     => 'Новости',
                ),
                'catalog'   => array(
                    'label'     => 'Каталог',
                ),
                'gallery'   => array(
                    'label'     => 'Галерея',
                ),
                'feedback'  => array(
                    'label'     => 'Обратная связь',
                ),
            );
        }

        $ret    = array();
        foreach ( $this->available_modules as $key => $mod )
        {
            $ret[ $key ]    = $mod['label'];
        }
        return $ret;
    }

    function debugAll()
    {
        printVar($this->all);
    }

    /**
     * Искать структуру по маршруту
     * @param  $route
     * @return array
     */
    function findByRoute( $route )
    {
        foreach( $this->all as $data ) {
            if ( $data['alias'] == $route ) {
                return $data;
            }
        }
        $data = $this->db->fetch(
            "SELECT * FROM ".DBSTRUCTURE." WHERE alias = :route AND deleted = 0 LIMIT 1",
            DB::F_ASSOC,
            array(':route'=>$route)
        );
        if ( $data ) {
            $this->all[ $data['id'] ] = $data;
            return $data;
        }
        return false;
    }

    /**
     * Поиск
     * @param  $id
     * @return array
     */
    function find( $id )
    {
        if ( ! isset( $this->all[ $id ] ) ) {
            $data = $this->db->fetch("SELECT * FROM ".DBSTRUCTURE." WHERE id = '$id' AND deleted = 0 LIMIT 1");
            $this->all[ $id ] = $data;
        }
        return $this->all[ $id ];
    }

    /**
     * Поиск всех разделов сайта по условию
     * @param string $cond Условие
     * @return model_Structure
     */
    function findAll( $cond = '' )
    {
        $where = 'WHERE deleted = 0';
        if ( $cond ) {
            $where .= " AND {$cond} ";
        }

        $data_all = $this->db->fetchAll("SELECT * FROM ".DBSTRUCTURE." {$where} ORDER BY pos ASC");

        $this->all = $data_all;

        /*foreach ( $data_all as $key => $val )
        {
            if ( isset( $this->all[ $val['id'] ] ) ) {
                $data_all[ $key ] = $this->all[ $val['id'] ];
            }
            else {
                $this->all[ $val['id'] ] = $val;
            }
        }*/
        return $data_all;
    }

    function sortAll()
    {

    }

    /**
     * Найдет путь для страницы
     * @param int $id
     */
    function findPathJSON( $id )
    {
        $path = array();
        while( $id ) {
            $data = $this->db->fetch("SELECT * FROM ".DBSTRUCTURE." WHERE id = $id LIMIT 1");
            if ( $data ) {
                $path[] = array( 'id'=>$data['id'], 'name'=>$data['name'], 'url'=>$data['alias']);
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
     * Обновить или добавить массив в базу
     * @return bool
     */
    function update()
    {
        if ( $this->data['id'] ) {
            $this->data['path'] = $this->findPathJSON( $this->data['id'] );
        } else {
            $this->data['path'] = '';
        }

        $ret = $this->db->insertUpdate( DBSTRUCTURE, $this->data );

        if ( $ret ) {
            if ( ! $this->data['id'] ) {
                $this->data['id'] = $ret;
                $this->update();
            }
        }
        return $ret;
    }


    /**
     * Вернет значение для новой позиции для нового раздела
     * @param $parent_id
     */
    function getNextPos( $parent_id )
    {
        return $this->db->fetchOne("SELECT MAX(pos)+1 FROM ".DBSTRUCTURE." WHERE parent = '{$parent_id}' AND deleted = 0");
    }

    /**
     * Переключение
     * @param string $action    действие
     * @param int $id           идентификатор
     * @return bool
     */
    function switching( $action, $id )
    {
        $current = $this->find( $id );
        switch( $action ) {
            case 'on':
                $current['hidden'] = '0';
                break;
            case 'off':
                $current['hidden'] = '1';
                break;
            case 'delete':
                $current['deleted'] = '1';
                break;
            case 'up':
                return $this->moveUp( $id );
                break;
            case 'down':
                return $this->moveDown( $id );
                break;
            case '':
                break;
        }
        return (bool) $this->db->insertUpdate( DBSTRUCTURE, $current );
    }

    /**
     * Переместить раздел вверх
     * @param $id
     * @return bool
     */
    function moveUp( $id )
    {
        $current    = $this->find( $id );
        $some       = $this->db->fetch(
            "SELECT * FROM ".DBSTRUCTURE."
            WHERE deleted = 0 AND parent = '{$current['parent']}' AND pos < '{$current['pos']}'
            ORDER BY pos DESC LIMIT 1"
        );
        if ( ! $some ) {
            return true;
        }
        return $this->moveComplete( $current, $some );
    }

    /**
     * Переместить раздел вниз
     * @param $id
     * @return bool
     */
    function moveDown( $id )
    {
        $current    = $this->find( $id );
        $some       = $this->db->fetch(
            "SELECT * FROM ".DBSTRUCTURE."
            WHERE deleted = 0 AND parent = '{$current['parent']}' AND pos > '{$current['pos']}'
            ORDER BY pos LIMIT 1"
        );
        if ( ! $some ) {
            return true;
        }
        return $this->moveComplete( $current, $some );
    }

    /**
     * Осуществить перемещение в базе
     * @param array $part1
     * @param array $part2
     * @return bool
     */
    private function moveComplete( &$part1, &$part2 )
    {
        $old_pos        = $part2['pos'];
        $part2['pos']   = $part1['pos'];
        $part1['pos']   = $old_pos;
        if ( $this->db->insertUpdateMulti( DBSTRUCTURE, array( $part2, $part1 ) ) ) {
            return true;
        }
        App::$request->addFeedback('Раздел не был перемещен');
        return false;
    }


    /**
     * Создает дерево $this->tree по данным из $this->all
     * @param $parent
     */
    function createTree( $parent = 0 )
    {
        $this->parents = array();
        $this->findAll();
        // создаем массив, индексируемый по родителям
        foreach( $this->all as &$data ) {
            $this->parents[ $data['parent'] ][ $data['id'] ] =& $data;
        }
    }

    /**
     * Вернет HTML-меню
     * @param int $parent родитель
     * @param int $level уровень вложенности
     * @return string
     */
    function getMenu( $parent, $levelback = 1 )
    {
        $html = "";

        if ( count($this->parents) == 0 ) {
            $this->createTree();
        }

        if ( $levelback <= 0 ) {
            return '';
        }

        if ( ! isset($this->parents[ $parent ]) ) {
            return '';
        }

        $html .= '<ul>';
        $counter = count( $this->parents[ $parent ] );
        $total_count = $counter;
        foreach( $this->parents[ $parent ] as $branch )
        {
            if (   $branch['hidden'] == 0
                && $branch['protected'] <= App::$user->getPermission()
                && $branch['deleted'] == 0
            ) {
                $html .= "<li class='item-{$branch['id']}".($counter == $total_count?" first":($counter==1?" last":""))."'>";
                if ( $branch['id'] == App::$request->get('id') || $branch['alias']==$this->request->get('route') ) {
                    $html .= '<span>'.$branch['name'].'</span>';
                    //$html .= "<a ".href($branch['alias'])." class='active'>{$branch['name']}</a>";
                }
                else {
                    $html .= "<a ".href($branch['alias']).">{$branch['name']}</a>";
                }
                $html .= $this->getMenu( $branch['id'], $levelback - 1 );
                $html .= '</li>';
            }
            $counter--;
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Венет HTML-структуру
     * @return string
     */
    function createHtmlList()
    {
        $this->html = array();
        $this->returnListRecursive( 0 );
    }

    /**
     * Обходит массив дерева структуры и возвращает
     * на его основе HTML
     * @param $branches
     * @return array
     */
    protected function returnListRecursive( $parent, $level = 1 )
    {
        if ( isset($this->parents[ $parent ]) ) {
            $branches = $this->parents[ $parent ];
        }
        else {
            $branches = array();
        }

        $prefix = str_repeat("\t", $level);

        $this->html[] = $prefix."<ul parent='{$parent}'>";

        $count = count($branches);

        foreach( $branches as $i => $branch )
        {
            if ( $branch['action'] == 'doc' ) {
                continue;
            }

            $icon   = 'folder';
            $opened = isset($this->parents[ $branch['id'] ]);
            $icon   = $opened ? 'folder_explore' : $icon;
            $icon   = $branch['action'] == 'doc' ? 'page' : $icon;

            $li_class = '';
            if ( $level == 1 ) {
                $li_class = ' class="level_one"';
            }

            if ( $i == 0 && $level == 1  ) {
                $bicon = 'cross1';
            } else {
                if ( $i + 1 == $count ) {
                    $bicon = 'cross3';
                } else {
                    $bicon = 'cross2';
                }
            }

            $this->html[] =
            $prefix."<li{$li_class} parent='{$branch['parent']}' this='{$branch['id']}' pos='{$branch['pos']}'>
                    <span id='item{$branch['id']}' class='{$bicon}'>
                    <img src='/images/admin/icons/{$icon}.png' alt='' />
                    <a ".href('admin/edit',   array('edit'   => $branch['id'])).">{$branch['name']}</a>
                    <span class='tools'>
                        <a ".href('admin/edit',   array('edit'   => $branch['id']))."   title='Правка'>".icon('pencil', 'Правка')."</a>
                        <a ".href('admin/add',    array('add' => $branch['id']))."    title='Добавить'>".icon('add', 'Добавить')."</a>
                        <a ".href('admin', array('do'=>'delete','part'=>$branch['id']))." title='Удалить' class='do_delete'>".icon('delete', 'Удалить')."</a>".
                        //($branch['controller'] != 'page' ? '' : " <a class='link_add' page='{$branch['id']}' ".href('').">".icon('link_add', 'Добавить внешнюю связь').'</a> ' ).
                    "</span>
                    <span class='order'>".
                        ($branch['link'] ? '<a class="link_del" page="'.$branch['id'].'" '.href('').'>'.icon('link', 'Внешняя связь').'</a>' : '' ).
                        ($branch['hidden'] ?
                            " <a ".href('admin', array('do'=>'on','part'=>$branch['id'])).">".icon('lightbulb_off', 'Выключен')."</a>":
                            " <a ".href('admin', array('do'=>'off','part'=>$branch['id'])).">".icon('lightbulb', 'Включен'))."</a>".
                        " <a class='order-up'  ".href('admin', array('do'=>'up',  'part'=>$branch['id']))." title='Вверх'>".icon('arrow_up', 'Вверх')."</a>
                        <a class='order-down' ".href('admin', array('do'=>'down','part'=>$branch['id']))." title='Вниз'>".icon('arrow_down', 'Вниз')."</a>

                        <span class='id_number'>
                        #{$branch['id']}
                        </span>
                    </span>
                </span>";

            //if ( $opened ) {
            //    $this->returnListRecursive( $this->parents[ $branch['id'] ], $level + 1 );
            //}
            $this->returnListRecursive( $branch['id'], $level + 1 );
            $this->html[] = $prefix."</li>";
        }
        $this->html[] = $prefix."</ul>";
    }

    /**
     * Вернет объект формы
     * @return form_Form
     */
    function getForm()
    {
        if ( !isset($this->form) ) {
            $this->form = new forms_page_structure();
            $this->form->controller->setVariants($this->getAvaibleModules());
            $this->form->protected->setVariants($this->config->get('users.groups'));
        }
        return $this->form;
    }


}
