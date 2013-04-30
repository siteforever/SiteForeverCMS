<?php
/**
 * Модель структуры
 */

namespace Module\Page\Model;

use SimpleXMLElement;

use Sfcms\Form\Form;
use Forms_Page_Page;
use Sfcms\Model;
use Module\Page\Object\Page;
use Sfcms\Data\Collection;
use Sfcms\Model\Exception;

/**
 * Class PageModel
 * @package Module\Page\Model
 */
class PageModel extends Model
{
    /**
     * Массив, индексированный по $parent
     * @var array
     */
    public $parents;

    /**
     * Списков разделов в кэше
     * @var Collection
     */
    protected $all = null;

    public $html = array();

    /**
     * Форма редактирования
     * @var Form
     */
    private $form = null;

    protected $available_modules;

    /** @var array ControllerLink Cache */
    private $_controller_link = array();

    public function init()
    {
        $this->on(sprintf('%s.save.start', $this->eventAlias()), array($this,'onPageSaveStart'));
//        $this->on(sprintf('%s.save.success', $this->eventAlias()), array($this,'onPageSaveSuccess'));
    }

    public function relation()
    {
        return array(
            'Pages'  => array( self::HAS_MANY, 'Page', 'parent', 'where' => array('protected'=>0,'hidden'=>0,'deleted'=>0) ),
            'Parent' => array( self::BELONGS, 'Page', 'parent', 'where' => array('protected'=>0,'hidden'=>0,'deleted'=>0) ),
        );
    }

    public function onCreateTable()
    {
        /** @var $page Page */
        $page = $this->createObject();
        $page->parent   = 0;
        $page->name     = t('Home');
        $page->title    = t('Home');
        $page->template = 'index';
        $page->alias    = 'index';
        $page->date     = time();
        $page->update   = time();
        $page->pos      = 0;
        $page->controller = 'page';
        $page->action   = 'index';
        $page->content  = $this->app()->getTpl()->fetch('system:page.model.default');
        $page->author   = 1;
    }

    /**
     * Выбирает из базы и кэширует структуру страниц
     * @return array|Collection|null
     */
    public function getAll()
    {
        if ( null === $this->all ) {
            // Кэшируем структуру страниц
            $this->all = $this->findAll('deleted = ?',array(0),'pos');
        }
        return $this->all;
    }

    /**
     * Добавить в общую коллекцию страниц
     * @param Page $obj
     */
    public function addToAll( Page $obj )
    {
        $this->all->add( $obj );
        $this->parents[ $obj->parent ][ $obj->id ] = $obj;
    }

    /**
     * Отвечает за пересортировку
     * @param array $sort
     * @return mixed
     */
    public function resort( array $sort )
    {
        if ( 0 == count($sort) ) {
            return 'fail';
        }
        $pages  = $this->findAll('id IN ('.join(',',$sort).')');
        $sort   = array_flip($sort);

        foreach ( $pages as $pageObj ) {
            /** @var $pageObj Page */
            $pageObj->pos = $sort[$pageObj->id];
            $this->trigger(
                "plugin.page-{$pageObj->controller}.resort",
                new Model\ModelEvent($pageObj, $this)
            );
        }

        return 'done';
    }


    /**
     * @param $controller
     * @param $link
     *
     * @return Page
     */
    public function findByControllerLink( $controller, $link )
    {
        if ( isset( $this->_controller_link[$controller][$link] ) ) {
            return $this->_controller_link[$controller][$link];
        }
        /** @var $page Page */
        foreach ( $this->getAll() as $page ) {
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
    public function checkAlias( $alias = null )
    {
        $find = false;
        /** @var $page Page */
        foreach( $this->getAll() as $page ) {
            if ( $page->alias == $alias ) {
                $find = $page->id;
                break;
            }
        }
        return $find;
    }

    /**
     * @param \Sfcms\Model\ModelEvent $event
     *
     * @throws \Sfcms\Model\Exception
     */
    public function onPageSaveStart( Model\ModelEvent $event )
    {
        /** @var $obj Page  */
        $obj = $event->getObject();

        $pageId = $this->checkAlias( $obj->alias );
        if ( false !== $pageId && $obj->getId() != $pageId ) {
            throw new Exception( t( 'The page with this address already exists' ) );
        }

        $obj->path = $obj->createPath();

        // Настраиваем связь с модулями
        // @todo Должно определяться по имени модели, но в странице не указана связанная модель, а только контроллер
        $this->trigger(sprintf('plugin.page-%s.save.start', $obj->controller), $event);
//        $this->callPlugins( "{$obj->controller}:onSaveStart", $obj);
    }

    /**
     * Вернет список доступных модулей
     * Нужны для составления списка создания страницы в админке
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
        foreach ( $this->getAll() as $data ) {
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
            $this->getAll()->add( $obj );
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
     * Создает список $this->parents по данным из $this->all
     */
    public function createParentsIndex()
    {
        $this->parents = array();
        // создаем массив, индексируемый по родителям
        /** @var Page $obj */
        if ( count($this->parents) == 0 ) {
            foreach ( $this->getAll() as $obj ) {
                $this->parents[ $obj->parent ][ $obj->id ] = $obj;
            }
        }
    }

    /**
     * Вернет массив со списком разделов для указания в форме
     * @param int $parent
     * @param int $level
     * @return array
     */
    public function getSelectOptions( $parent = 0, $level = 0 )
    {
        $return = array('0' => t('No parent'));
        if ( ! $this->parents ) {
            $this->createParentsIndex();
        }
        /** @var $obj Page */
        foreach( $this->parents[ $parent ] as $obj ) {
            $return[ $obj->id ] = str_repeat('&nbsp;', $level * 4) . $obj->name;
            if ( isset( $this->parents[$obj->id] ) ) {
                $return += $this->getSelectOptions( $obj->id, $level + 1 );
            }
        }
        return $return;
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
            $this->form->getField( 'protected' )->setVariants( self::getModel( 'User' )->getGroups() );
        }
        return $this->form;
    }
}
