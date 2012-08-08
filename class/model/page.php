<?php
/**
 * Модель структуры
 */
class Model_Page extends Sfcms_Model
{

    /**
     * Массив, индексируемый по $parent
     * @var array
     */
    public $parents;

    /**
     * Списков разделов в кэше
     * @var Data_Collection
     */
    public $all = null;

    public $html = array();

    /**
     * Форма редактирования
     * @var form_Form
     */
    private $form = null;


    protected $available_modules;

    /** @var array ControllerLink Cache */
    private $_controller_link = array();

    public function Init()
    {
        // Кэшируем структуру страниц
        $this->all = $this->findAll('deleted = ?',array(0),'pos');
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
                '<a ' . Siteforever::html()->href( 'page/admin' ) . '>панель управления</a></p>',
            'author'        => '1',
        ));
    }

    /**
     * @param array $sort
     * @return mixed
     */
    public function resort( array $sort )
    {
        $upd  = array();

        foreach ( $sort as $pos => $id ) {
            $upd[ ] = array(
                'id' => $id,
                'pos'=> $pos
            );
        }

        if ($this->db->insertUpdateMulti( $this->getTable(), $upd )) {
            $this->request->setResponse( 'errno', 0 );
            $this->request->setResponse( 'error', 'ok' );
            return 'done';
        } else {
            $this->request->setResponse( 'errno', 1 );
            $this->request->setResponse( 'error', t( 'Data not saved' ) );
        }
        return 'fail';
    }


    /**
     * @param $controller
     * @param $link
     *
     * @return Data_Object_Page
     */
    public function findByControllerLink( $controller, $link )
    {
        if ( isset( $this->_controller_link[$controller][$link] ) ) {
            return $this->_controller_link[$controller][$link];
        }
        /** @var $page Data_Object_Page */
        foreach ( $this->all as $page ) {
            if ( $link == $page->link && $controller == $page->controller ) {
                $this->_controller_link[$controller][$link] = $page;
                return $page;
            }
        }
        return null;
    }

    /**
     * Проверить алиас страницы
     * @param $alias
     * @return bool|int
     */
    public function checkAlias( $alias )
    {
        $find = false;
        $alias = null;
        /** @var $page Data_Object_Page */
        foreach( $this->all as $page ) {
            if ( $page->alias == $alias ) {
                $find = $page->id;
                break;
            }
        }
        return $find;
    }

    /**
     * @param Data_Object_Page $obj
     * @return bool
     * @throws Sfcms_Model_Exception
     */
    public function onSaveStart( Data_Object $obj = null )
    {
        $pageId = $this->checkAlias( $obj->alias );
        if ( false !== $pageId && $obj->getId() != $pageId ) {
            throw new Sfcms_Model_Exception( t( 'The page with this address already exists' ) );
        }

        $obj->path = $obj->createPath();


        // Настраиваем связь с модулями
        if ( in_array( $obj->controller, array('news','gallery','catalog') ) ) {

            switch ( $obj->controller ) {
                case 'news': $model = $this->getModel('NewsCategory'); break;
                case 'gallery': $model = $this->getModel('GalleryCategory'); break;
                case 'catalog': $model = $this->getModel('Catalog'); break;
                default: $model = $this;
            }

            /** @var $category Data_Object */
            if ( $obj->link ) {
                $category = $model->find( $obj->link );
            } else {
                $category = $model->createObject();
            }
            $category->name = $obj->name;
            $category->hidden = $obj->hidden;
            $category->protected = $obj->protected;
            $category->deleted = $obj->deleted;

            if ( 'catalog' == $obj->controller ) {
                /** @var $category Data_Object_Catalog  */
                $category->cat = 1;

                if ( $obj->parent ) {
                    /** @var $parentPage Data_Object_Page */
                    $parentPage = $this->find( $obj->parent );
                    if ( $parentPage->controller == $obj->controller && $parentPage->link ) {
                        $category->parent = $parentPage->link;
                    } else {
                        $category->parent = 0;
                    }
                }
            }
            $category->save();
            if ( ! $obj->link ) {
                $obj->link = $category->getId();
            }
        }
        $this->log( $obj->controller . '.' . $obj->link, __METHOD__.':'.__LINE__ );
        return true;
    }

    /**
     * @param Data_Object_Page $obj
     *
     * @return bool
     */
    public function onSaveSuccess( Data_Object $obj = null )
    {
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
            $this->all->add( $obj );
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
     * Создает дерево $this->tree по данным из $this->all
     */
    public function createTree()
    {
        $this->parents = array();
        if ( count( $this->all ) == 0 ) {
            $this->all = $this->findAll(
                array(
                    'cond'  => 'deleted = 0',
                    'order' => 'pos',
                )
            );
        }
        // создаем массив, индексируемый по родителям
        /** @var Data_Object_Page $obj */
        foreach ( $this->all as $obj ) {
            $this->parents[ $obj->parent ][ $obj->id ] = $obj;
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
        } else {
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

            $link = '';
            if ( 'page' != $branch[ 'controller' ] ) {
                $linkUrl = '#';
                switch ( $branch[ 'controller' ] ) {
                    case 'catalog':
                        $linkUrl = "/catalog/category/edit/{$branch[ 'link' ]}";
                        break;
                    case 'gallery':
                        $linkUrl = "/gallery/editcat/id/{$branch[ 'link' ]}";
                        break;
                    case 'news':
                        $linkUrl = "/news/catedit/id/{$branch[ 'link' ]}";
                        break;
                }
                $link = "<a href='{$linkUrl}'>" . icon( 'link', 'Перейти к модулю' ) . '</a>';
            }

            $this->html[ ] =
                $prefix . "<li{$li_class} parent='{$branch['parent']}' this='{$branch['id']}' pos='{$branch['pos']}'>"
                    . "<span id='item{$branch['id']}' class='{$bicon}'>" . icon( $this->selectIcon( $branch ) )
                    . " <a class='edit' title='".t('Edit page')."' " . href(
                    null, array(
                        'controller'=> 'page',
                        'action'    => 'edit',
                        'edit'      => $branch[ 'id' ]
                    )
                ) . ">{$branch['name']}</a>"
                    . "<span class='tools'>"
                    . $link
                    . "<a class='edit' title='".t('Edit page')."' " . href(
                    null, array(
                        'controller'=> 'page',
                        'action'    => 'edit',
                        'edit'      => $branch[ 'id' ]
                    )
                ) . " title='Правка'>" . icon( 'pencil', 'Правка' ) . "</a>"
                    . "<a class='add' rel='{$branch[ 'id' ]}' title='".t('Create page')."' " . href(
                    null, array(
                        'controller'=> 'page',
                        'action'    => 'create',
                    )
                ) . "    title='Добавить'>" . icon( 'add', 'Добавить' ) . "</a>"
                    . "<a class='do_delete' " . href(
                    null, array(
                        'controller'=> 'page',
                        'action'    => 'delete',
                        'id'        => $branch[ 'id' ]
                    )
                ) . " title='Удалить'>" . icon( 'delete', 'Удалить' ) . "</a>"
                    . "</span>"
                    . "<span class='order'>"
                    . $this->getOrderHidden( $branch[ 'id' ], $branch[ 'hidden' ] )

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
            $this->form->getField( 'protected' )->setVariants( Sfcms_Model::getModel( 'User' )->getGroups() );
        }
        return $this->form;
    }
}
