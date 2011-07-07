<?php
/**
 * Модель структуры
 */
class Model_Page extends Model
{

    /**
     * Массив, индексируемый по $parent
     * @var array
     */
    public $parents;

    /**
     * Списков разделов в кэше
     * @var array
     */
    public $all     = array();

    public $html    = array();

    /**
     * Форма редактирования
     * @var form_Form
     */
    private $form   = null;


    protected   $available_modules;

    function Init()
    {
        // Кэшируем структуру страниц
        $this->all = $this->findAll(array('cond'=>'deleted = 0','order' => 'pos',));
    }


    function onCreateTable()
    {
        $this->db->insert( $this->table, array(
            'parent'    => '0',
            'name'      => 'Главная',
            'template'  => 'index',
            'alias'     => 'index',
            'date'      => time(),
            'update'    => time(),
            'pos'       => '0',
            'controller'    => 'page',
            'action'    => 'index',
            'title'     => 'Главная',
            'content'   => '<p>Эта страница была создана в автоматическом режиме</p>'.
                           '<p>Чтобы перейти к управлению сайтом, зайдите в '.
                           '<a '.Siteforever::html()->href('admin').'>панель управления</a></p>',
            'author'    => '1',
        ) );
    }

    /**
     * @param  $sort
     * @return int
     */
    public function resort( $sort )
    {
        $sort = array_flip($sort);
        $upd = array();

        foreach( $sort as $id => $pos ) {
            $upd[] = array('id'=>$id, 'pos'=>$pos);
        }

        if ( $this->db->insertUpdateMulti( $this->getTable(), $upd ) )
        {
            $this->request->setResponse('errno', 0);
            $this->request->setResponse('error', 'ok');
            return 1;
        }
        else {
            $this->request->setResponse('errno', 1);
            $this->request->setResponse('error', t('Data not saved'));
        }
        return 0;
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
            if ( $data->alias == $route ) {
                return $data;
            }
        }

        $obj    = $this->find(array(
               'cond'       => 'alias = :route AND deleted = 0',
               'params'     => array(':route'=>$route),
          ));

        if ( $obj ) {
            $this->all[ $obj->getId() ] = $obj;
            return $obj;
        }
        return false;
    }

    /**
     * Найдет путь для страницы
     * @param int $id
     * @return string
     */
    function findPathJSON( $id )
    {
        $path = array();
        while( $id ) {
            $obj   = $this->find( $id );
            if ( $obj ) {
                $path[] = array( 'id'=>$obj['id'], 'name'=>$obj['name'], 'url'=>$obj['alias']);
                $id = $obj['parent'];
                continue;
            }
            $id = 0;
        }
        $path = array_reverse( $path );
        return json_encode( $path );
    }

    /**
     * Вернет значение для новой позиции для нового раздела
     * @param $parent_id
     * @return int
     */
    function getNextPos( $parent_id )
    {
        $max   = $this->db->fetchOne(
            "SELECT MAX(pos)
            FROM {$this->table}
            WHERE parent = :parent AND deleted = 0",
            array(':parent'=>$parent_id)
        );
        if ( ! $max ) return 0;
        return ++$max;
    }

    /**
     * Переключение
     * @param string $action    действие
     * @param int $id           идентификатор
     * @return mixed
     */
    function switching( $action, $id )
    {
        $current = $this->find( $id );
        switch( $action ) {
            case 'on':
                $current['hidden']  = '0';
                break;
            case 'off':
                $current['hidden']  = '1';
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
        return $this->save( $current );
    }

    /**
     * Переместить раздел вверх
     * @param $id
     * @return bool
     */
    function moveUp( $id )
    {
        $current    = $this->find( $id );

        $some   = $this->find(array(
            'cond'      => 'deleted = 0 AND parent = :parent AND pos < :pos',
            'params'    => array(':parent'=>$current['parent'], ':pos'=>$current['pos']),
            'order'     => 'pos DESC',
        ));

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

        $some   = $this->find(array(
            'cond'      => 'deleted = 0 AND parent = :parent AND pos < :pos',
            'params'    => array(':parent'=>$current['parent'], ':pos'=>$current['pos']),
            'order'     => 'pos DESC',
        ));
        if ( ! $some ) {
            return true;
        }
        return $this->moveComplete( $current, $some );
    }

    /**
     * Осуществить перемещение в базе
     * @param Data_Object_Page $page1
     * @param Data_Object_Page $page2
     * @return bool
     */
    private function moveComplete( Data_Object_Page $page1, Data_Object_Page $page2 )
    {
        $old_pos        = $page2['pos'];
        $page2['pos']   = $page1['pos'];
        $page1['pos']   = $old_pos;
        if ( $this->db->insertUpdateMulti( $this->table, array( $page2, $page1 ) ) ) {
            return true;
        }
        $this->request->addFeedback('Раздел не был перемещен');
        return false;
    }


    /**
     * Создает дерево $this->tree по данным из $this->all
     * @param int $parent
     */
    function createTree( $parent = 0 )
    {
        $this->parents = array();
        if ( count($this->all) == 0 ) {
            $this->all = $this->findAll(array('cond'=>'deleted = 0','order' => 'pos',));
        }
        // создаем массив, индексируемый по родителям
        /**
         * @var Data_Object_Page $data
         */
        foreach( $this->all as $data ) {
            $this->parents[ $data['parent'] ][ $data['id'] ] = $data;
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
        $html = '';

        if ( count($this->parents) == 0 ) {
            $this->createTree();
        }

        if ( $levelback <= 0 ) {
            return '';
        }

        if ( ! isset($this->parents[ $parent ]) ) {
            return '';
        }

        $html          .= '<ul>';
        $counter        = count( $this->parents[ $parent ] );
        $total_count    = $counter;

        foreach ( $this->parents[ $parent ] as $branch )
        {
            if (   $branch['hidden'] == 0
                //&& $this->app()->getUser()->hasPermission( $branch['protected'] )
                && $branch['deleted'] == 0 )
            {
                $html .= "<li class='item-{$branch['id']}".($counter == $total_count?" first":($counter==1?" last":""))."'>";
                if ( $branch['id'] == $this->request->get('id') || $branch['alias']==$this->request->get('route') ) {
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
     * на его основе HTML для админки
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

            $icon   = $branch['controller'] == 'page' ? 'page' : 'folder';
            $icon   = $branch['controller'] == 'news' ? 'folder_feed' : $icon;
            $icon   = $branch['controller'] == 'gallery' ? 'folder_picture' : $icon;
            $icon   = $branch['controller'] == 'catalog' ? 'folder_table' : $icon;
            $icon   = isset($this->parents[ $branch['id'] ]) ? 'folder_explore' : $icon;

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
                    <span id='item{$branch['id']}' class='{$bicon}'>".icon($icon).
                    " <a ".href('admin',   array('edit'   => $branch['id'])).">{$branch['name']}</a>".
                    "<span class='tools'>".
                        "<a ".href('admin', array('edit'   => $branch['id']))."   title='Правка'>".icon('pencil', 'Правка')."</a>".
                        "<a ".href('admin', array('add' => $branch['id']))."    title='Добавить'>".icon('add', 'Добавить')."</a>".
                        "<a ".href('admin', array('do'=>'delete','part'=>$branch['id']))." title='Удалить' class='do_delete'>".icon('delete', 'Удалить')."</a>".
                            //($branch['controller'] != 'page' ? '' : " <a class='link_add' page='{$branch['id']}' ".href('').">".icon('link_add', 'Добавить внешнюю связь').'</a> ' ).
                    "</span>".
                    "<span class='order'>".
                        ($branch['controller']=='page' ? '' : '<a class="link_del" page="'.$branch['id'].'" '.href('').'>'.icon('link', 'Внешняя связь').'</a>' ).
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
        if ( ! isset($this->form) ) {
            $this->form = new Forms_Page_Page();
            $this->form->getField('controller')->setVariants($this->getAvaibleModules());
            $this->form->getField('protected')->setVariants(Model::getModel('User')->getGroups());
        }
        return $this->form;
    }

//    /**
//     * Класс для контейнера данных
//     * @return string
//     */
//    public function objectClass()
//    {
//        return 'Data_Object_Page';
//    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_Page';
    }
}