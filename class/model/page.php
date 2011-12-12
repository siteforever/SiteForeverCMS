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
    public $all = array();

    public $html = array();

    /**
     * Форма редактирования
     * @var form_Form
     */
    private $form = null;


    protected $available_modules;

    public function Init()
    {
        // Кэшируем структуру страниц
        $this->all = $this->findAll(
            array(
                'cond'  => 'deleted = 0',
                'order' => 'pos',
            )
        );
    }


    public function onCreateTable()
    {
        $this->db->insert(
            $this->table, array(
            'parent'        => '0',
            'name'          => 'Главная',
            'template'      => 'index',
            'alias'         => 'index',
            'date'          => time(),
            'update'        => time(),
            'pos'           => '0',
            'controller'    => 'page',
            'action'        => 'index',
            'title'         => 'Главная',
            'content'       => '<p>Эта страница была создана в автоматическом режиме</p>' .
                '<p>Чтобы перейти к управлению сайтом, зайдите в ' .
                '<a ' . Siteforever::html()->href( 'admin' ) . '>панель управления</a></p>',
            'author'        => '1',
        )
        );
    }

    /**
     * @param  $sort
     *
     * @return int
     */
    public function resort( $sort )
    {
        $sort = array_flip( $sort );
        $upd  = array();

        foreach ( $sort as $id => $pos ) {
            $upd[ ] = array(
                'id' => $id,
                'pos'=> $pos
            );
        }

        if ($this->db->insertUpdateMulti( $this->getTable(), $upd )) {
            $this->request->setResponse( 'errno', 0 );
            $this->request->setResponse( 'error', 'ok' );
            return 1;
        }
        else {
            $this->request->setResponse( 'errno', 1 );
            $this->request->setResponse( 'error', t( 'Data not saved' ) );
        }
        return 0;
    }

    /**
     * @param Data_Object_Page $obj
     *
     * @return bool
     */
    public function onSaveStart( $obj = null )
    {
        if (null === $obj) {
            return false;
        }

        //        $attributes = $obj->getAttributes();

        // Проверить алиас страницы
        $page = $this->find(
            array(
                'condition'=> '`alias`=?',
                'params'   => array( $obj->getAlias() )
            )
        );
        if ($page && $page->getId() != $obj->getId()) {
            throw new ModelException( t( 'The page with this address already exists' ) );
        }

        //        $obj->setAttributes( $attributes ); // Т.к. поиск $page может переписать параметры
        //        var_dump($obj->controller);

        /**
         * @var Model_Alias $alias_model
         */
        $alias_model = $this->getModel( 'Alias' );
        if ($obj->alias_id) {
            $alias = $alias_model->find( $obj->alias_id );
        }
        else {
            $alias = $alias_model->findByAlias( $obj->getAlias() );
        }
        //        var_dump($alias->getAttributes());


        if (null !== $alias) {
            if (!$obj->getId()) {
                // если наш объект еще не создан, значит у кого-то уже есть такой алиас
                throw new ModelException( t( 'The alias with this address already exists' ), 1 );
            }

            if ($obj->alias_id && $obj->alias_id != $alias->getId()) {
                throw new ModelException( t( 'The alias with this address already exists' ), 2 );
            }
            //            else {
            //                $route  = $obj->createUrl();
            //                var_dump($route);
            //                var_dump($alias->url);
            //                if ( $alias->url != $route ) {
            //                    // если адреса не соответствуют
            //                    throw new ModelException('Такой алиас уже существует');
            //                }
            //            }
        }

        $obj->path = $obj->createPath();
        return true;
    }

    /**
     * @param Data_Object_Page $obj
     *
     * @return bool
     */
    public function onSaveSuccess( $obj = null )
    {
        /**
         * @var Model_Alias $alias_model
         */
        $alias_model = $this->getModel( 'Alias' );

        if ($obj->alias_id) {
            $alias = $alias_model->find( $obj->alias_id );
        }
        else {
            $alias = $alias_model->findByAlias( $obj->getAlias() );
        }

        if (null === $alias) {
            $alias = $alias_model->createObject();
        }

        $alias->alias = $obj->getAlias();
        $alias->url   = $obj->createUrl();

        //        $alias->controller  = $obj->controller;
        //        $alias->action      = $obj->action;
        //        $alias->params      = serialize(array('id'=>$obj->getId()));
        $alias->save();
        return true;
    }

    /**
     * Вернет список доступных модулей
     * @return array|null
     */
    public function getAvaibleModules()
    {
        if (is_null( $this->available_modules )) {

            $content          = '';
            $controllers_file = '/protected/controllers.xml';
            if (file_exists( ROOT . $controllers_file )) {
                $content = file_get_contents( ROOT . $controllers_file );
            }
            elseif (ROOT != SF_PATH && file_exists( SF_PATH . $controllers_file )) {
                $content = file_get_contents( SF_PATH . $controllers_file );
            }

            if (!$content) {
                return array();
            }

            $xml_controllers = new SimpleXMLElement( $content );

            $this->available_modules = array();

            foreach ( $xml_controllers->children() as $child ) {
                $this->available_modules[ (string) $child[ 'name' ] ] = array( 'label'=> (string) $child->label );
            }
        }

        $ret = array();
        foreach ( $this->available_modules as $key => $mod )
        {
            $ret[ $key ] = $mod[ 'label' ];
        }
        return $ret;
    }

    /**
     * Искать структуру по маршруту
     * @param  $route
     *
     * @return array
     */
    public function findByRoute( $route )
    {
        foreach ( $this->all as $data ) {
            if ($data->alias == $route) {
                return $data;
            }
        }

        $obj = $this->find(
            array(
                'cond'       => 'alias = :route AND deleted = 0',
                'params'     => array( ':route'=> $route ),
            )
        );

        if ($obj) {
            $this->all[ $obj->getId() ] = $obj;
            return $obj;
        }
        return false;
    }

    /**
     * Найдет путь для страницы
     * @param int $id
     *
     * @return string
     */
    public function findPathJSON( $id )
    {
        $path = array();
        while ( $id ) {
            $obj = $this->find( $id );
            if ($obj) {
                $path[ ] = array(
                    'id'  => $obj[ 'id' ],
                    'name'=> $obj[ 'name' ],
                    'url' => $obj[ 'alias' ]
                );
                $id      = $obj[ 'parent' ];
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
     *
     * @return int
     */
    public function getNextPos( $parent_id )
    {
        $max = $this->db->fetchOne(
            "SELECT MAX(pos) "
                . "FROM {$this->table} "
                . "WHERE parent = ? AND deleted = 0",
            array( $parent_id )
        );
        if (!$max) {
            return 0;
        }
        return ++$max;
    }

    /**
     * Переключение
     * @param string $action       действие
     * @param int    $id           идентификатор
     *
     * @return mixed
     */
    public function switching( $action, $id )
    {
        $current = $this->find( $id );
        switch ( $action ) {
            case 'on':
                $current[ 'hidden' ] = '0';
                break;
            case 'off':
                $current[ 'hidden' ] = '1';
                break;
            case 'delete':
                $current[ 'deleted' ] = '1';
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
     *
     * @return bool
     */
    public function moveUp( $id )
    {
        $current = $this->find( $id );

        $some = $this->find(
            array(
                'cond'      => 'deleted = 0 AND parent = :parent AND pos < :pos',
                'params'    => array(
                    ':parent'=> $current[ 'parent' ],
                    ':pos'   => $current[ 'pos' ]
                ),
                'order'     => 'pos DESC',
            )
        );

        if (!$some) {
            return true;
        }
        return $this->moveComplete( $current, $some );
    }

    /**
     * Переместить раздел вниз
     * @param $id
     *
     * @return bool
     */
    public function moveDown( $id )
    {
        $current = $this->find( $id );

        $some = $this->find(
            array(
                'cond'      => 'deleted = 0 AND parent = :parent AND pos < :pos',
                'params'    => array(
                    ':parent'=> $current[ 'parent' ],
                    ':pos'   => $current[ 'pos' ]
                ),
                'order'     => 'pos DESC',
            )
        );
        if (!$some) {
            return true;
        }
        return $this->moveComplete( $current, $some );
    }

    /**
     * Осуществить перемещение в базе
     * @param Data_Object_Page $page1
     * @param Data_Object_Page $page2
     *
     * @return bool
     */
    private function moveComplete( Data_Object_Page $page1, Data_Object_Page $page2 )
    {
        $old_pos        = $page2[ 'pos' ];
        $page2[ 'pos' ] = $page1[ 'pos' ];
        $page1[ 'pos' ] = $old_pos;
        if ($this->db->insertUpdateMulti( $this->table, array( $page2, $page1 ) )) {
            return true;
        }
        $this->request->addFeedback( 'Раздел не был перемещен' );
        return false;
    }

    /**
     * Создает дерево $this->tree по данным из $this->all
     *
     * @param int $parent
     */
    public function createTree( $parent = 0 )
    {
        $this->parents = array();
        if (count( $this->all ) == 0) {
            $this->all = $this->findAll(
                array(
                    'cond'  => 'deleted = 0',
                    'order' => 'pos',
                )
            );
        }
        // создаем массив, индексируемый по родителям
        /**
         * @var Data_Object_Page $data
         */
        foreach ( $this->all as $data ) {
            $this->parents[ $data[ 'parent' ] ][ $data[ 'id' ] ] = $data;


        }
    }

    /**
     * Вернет HTML-меню
     *
     * @param int             $parent
     * @param int             $levelback
     * @param DOMElement|null $node
     *
     * @return string
     */
    public function getMenu( $parent, $levelback = 1, DOMElement $node = null )
    {
        $do_return = false;
        if (null === $node) {
            $do_return = true;
            $dom       = new DOMDocument( '1.0', 'utf-8' );
            $dom->appendChild( $node = $dom->createElement( 'div' ) );
        }
        else {
            $dom = $node->ownerDocument;
        }

        if (count( $this->parents ) == 0) {
            $this->createTree();
        }

        if ($levelback <= 0) {
            return '';
        }

        if (!isset( $this->parents[ $parent ] )) {
            return '';
        }

        /**
         * @var DOMElement $ul
         */
        $node->appendChild( $ul = $dom->createElement( 'ul' ) );

        $counter     = count( $this->parents[ $parent ] );
        $total_count = $counter;

        foreach ( array_reverse( $this->parents[ $parent ], 1 ) as $obj ) {
            if ($obj[ 'hidden' ] == 0 && $obj[ 'deleted' ] == 0) {
                break;
            }
        }

        foreach ( $this->parents[ $parent ] as $branch )
        {
            if ($branch[ 'hidden' ] == 0 && $branch[ 'deleted' ] == 0) {
                $active = $branch[ 'alias' ] == $this->request->get( 'route' );

                /**
                 * @var DOMElement $li
                 */
                $ul->appendChild( $li = $dom->createElement( 'li' ) );
                /**
                 * @var DOMElement $a
                 */
                $li->appendChild( $a = $dom->createElement( 'a', $branch[ 'name' ] ) );
                $a->setAttribute( 'href', $this->app()->getRouter()->createLink( $branch[ 'alias' ] ) );

                $classes = array( 'item-' . $branch[ 'id' ] );

                if ($counter == $total_count) {
                    $classes[ ] = 'first';
                }

                if ($branch[ 'id' ] == $obj[ 'id' ]) {
                    $classes[ ] = 'last';
                }

                if ($active) {
                    $classes[ ] = 'active';
                    $a->setAttribute( 'class', 'active' );
                }
                $li->setAttribute( 'class', join( ' ', $classes ) );
                $this->getMenu( $branch[ 'id' ], $levelback - 1, $li );
            }
            $counter--;
        }

        if (!$do_return) {
            return '';
        }
        $dom->formatOutput = true;
        return $dom->saveHTML();
    }

    /**
     * Венет HTML-структуру
     * @return string
     */
    public function createHtmlList()
    {
        $this->html = array();
        $this->returnListRecursive( 0 );
    }

    /**
     * Обходит массив дерева структуры и возвращает
     * на его основе HTML для админки
     *
     * @param int $parent
     * @param int $level
     *
     * @return array
     */
    protected function returnListRecursive( $parent, $level = 1 )
    {
        if (isset( $this->parents[ $parent ] )) {
            $branches = $this->parents[ $parent ];
        }
        else {
            $branches = array();
        }

        $prefix = str_repeat( "\t", $level );

        $this->html[ ] = $prefix . "<ul parent='{$parent}'>";

        $count = count( $branches );

        foreach ( $branches as $i => $branch )
        {
            if ($branch[ 'action' ] == 'doc') {
                continue;
            }

            $li_class = '';
            if ($level == 1) {
                $li_class = ' class="level_one"';
            }

            if ($i == 0 && $level == 1) {
                $bicon = 'cross1';
            }
            else {
                if ($i + 1 == $count) {
                    $bicon = 'cross3';
                }
                else {
                    $bicon = 'cross2';
                }
            }

            $this->html[ ] =
                $prefix . "<li{$li_class} parent='{$branch['parent']}' this='{$branch['id']}' pos='{$branch['pos']}'>"
                    . "<span id='item{$branch['id']}' class='{$bicon}'>" . icon( $this->selectIcon( $branch ) )
                    . " <a " . href(
                    null, array(
                    'controller'=> 'page',
                    'action'    => 'edit',
                    'edit'      => $branch[ 'id' ]
                )
                ) . ">{$branch['name']}</a>"
                    . "<span class='tools'>"
                    . "<a " . href(
                    null, array(
                    'controller'=> 'page',
                    'action'    => 'edit',
                    'edit'      => $branch[ 'id' ]
                )
                ) . " title='Правка'>" . icon( 'pencil', 'Правка' ) . "</a>"
                    . "<a " . href(
                    null, array(
                    'controller'=> 'page',
                    'action'    => 'add',
                    'add'       => $branch[ 'id' ]
                )
                ) . "    title='Добавить'>" . icon( 'add', 'Добавить' ) . "</a>"
                    . "<a " . href(
                    null, array(
                    'controller'=> 'page',
                    'action'    => 'admin',
                    'do'        => 'delete',
                    'part'      => $branch[ 'id' ]
                )
                ) . " title='Удалить' class='do_delete'>" . icon( 'delete', 'Удалить' ) . "</a>"
                    . "</span>"
                    . "<span class='order'>"
                    . ( $branch[ 'controller' ] == 'page'
                    ? ''
                    : '<a class="link_del" page="' . $branch[ 'id' ] . '" ' . href( '' ) . '>' . icon(
                        'link', 'Внешняя связь'
                    ) . '</a>'
                )
                    . $this->getOrderHidden( $branch[ 'id' ], $branch[ 'hidden' ] )
                    //                        ."<a class='order-up'  ".href('admin', array('do'=>'up',  'part'=>$branch['id']))." title='Вверх'>"
                    //                            .icon('arrow_up', 'Вверх')."</a>"
                    //                        ."<a class='order-down' ".href('admin', array('do'=>'down','part'=>$branch['id']))." title='Вниз'>"
                    //                            .icon('arrow_down', 'Вниз')."</a>"

                    . "<span class='id_number'>#{$branch['id']}</span>"
                    . "</span>"
                    . "</span>";

            $this->returnListRecursive( $branch[ 'id' ], $level + 1 );
            $this->html[ ] = $prefix . "</li>";
        }
        $this->html[ ] = $prefix . "</ul>";
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
            'page', 'hidden', array( 'id'=> $id )
        ) . "' class='order_hidden'>";
        $return .= $hidden ? icon( 'lightbulb_off', 'Выключен' ) : icon( 'lightbulb', 'Включен' );
        $return .= "</a>";
        return $return;
    }

    /**
     * @param $branch
     *
     * @return string
     */
    private function selectIcon( $branch )
    {
        $result = $branch[ 'controller' ] == 'page' ? 'page' : 'folder';
        $result = $branch[ 'controller' ] == 'news' ? 'folder_feed' : $result;
        $result = $branch[ 'controller' ] == 'gallery' ? 'folder_picture' : $result;
        $result = $branch[ 'controller' ] == 'catalog' ? 'folder_table' : $result;
        $result = isset( $this->parents[ $branch[ 'id' ] ] ) ? 'folder_explore' : $result;
        return $result;
    }

    /**
     * Вернет объект формы
     * @return Forms_Page_Page
     */
    public function getForm()
    {
        if (!isset( $this->form )) {
            $this->form = new Forms_Page_Page();
            $this->form->getField( 'controller' )->setVariants( $this->getAvaibleModules() );
            $this->form->getField( 'protected' )->setVariants( Model::getModel( 'User' )->getGroups() );
        }
        return $this->form;
    }
}
