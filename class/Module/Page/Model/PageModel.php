<?php
namespace Module\Page\Model;

use SimpleXMLElement;

use Sfcms\Model;
use Module\Page\Form\PageForm;
use Module\Page\Object\Page;
use Sfcms\Data\Collection;
use Sfcms\Model\Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

/**
 * Pages structure
 *
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

    protected $availableModules;

    /** @var array ControllerLink Cache */
    private $_controller_link = array();

    /** @var bool Filled router custom routes */
    protected static $routerFilled = false;

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
        $page->name     = $this->t('Home');
        $page->title    = $page->name;
        $page->template = 'index';
        $page->alias    = 'index';
        $page->date     = time();
        $page->update   = time();
        $page->pos      = 0;
        $page->controller = 'page';
        $page->action   = 'index';
        $page->content  = $this->app()->getTpl()->fetch('page.model.default');
        $page->author   = 1;
        $page->save();
    }

    /**
     * Выбирает из базы и кэширует структуру страниц
     * @return Page[]|null
     */
    public function getAll()
    {
        if ( null === $this->all ) {
            // Кэшируем структуру страниц
            $this->all = $this->findAll('deleted = ?', array(0), 'pos');
        }
        return $this->all;
    }

    /**
     * Добавить в общую коллекцию страниц
     * @param Page $obj
     */
    public function addToAll( Page $obj )
    {
        $this->getAll()->add( $obj );
        if ($obj->parent) {
            $parents =& $this->getParents();
            $parents[$obj->parent][$obj->id] = $obj;
        }
    }

    /**
     * Filling route table from database
     *
     * @param Router $router
     *
     * @return Router
     */
    public function fillRoutes(Router $router)
    {
        if (static::$routerFilled) {
            return $router;
        }
        $start = microtime(1);
        /** @var array $page */
        foreach ($this->getAll() as $page) {
            $default = array(
                '_controller' => $page['controller'],
                '_action' => $page['action'],
                '_id' => $page['id'],
                '_template' => $page['template'],
                '_system' => $page['system'],
                'alias' => $page['null']
            );
            $router->getRouteCollection()->add($page['alias'],
                new Route('/' . $page['alias'], $default));
        }
        foreach ($this->getAll() as $page) {
            if (!in_array($page['controller'], array('news', 'catalog', 'gallery'))) {
                continue;
            }
            $default = array(
                '_controller' => $page['controller'],
                '_action' => $page['action'],
                '_id' => $page['id'],
                '_template' => $page['template'],
                '_system' => $page['system'],
                'alias' => $page['null']
            );
            $router->getRouteCollection()->add($page['alias'].'_alias_',
                new Route('/' . $page['alias'] . '/{alias}', $default)
            );
        }
        $this->log('Loading aliases: ' . round(microtime(1) - $start, 3) . ' sec');
        static::$routerFilled = true;
        return $router;
    }

    /**
     * Отвечает за пересортировку
     * @param array $sort
     * @return mixed
     */
    public function resort( array $sort )
    {
        if ( 0 == count($sort) ) {
            return false;
        }
        $pages  = $this->findAll('id IN ('.join(',',$sort).')');
        $sort   = array_flip($sort);

        foreach ($pages as $pageObj) {
            /** @var $pageObj Page */
            $pageObj->pos = $sort[$pageObj->id];
            $this->trigger(
                "plugin.page-{$pageObj->controller}.resort",
                new Model\ModelEvent($pageObj, $this)
            );
        }

        return true;
    }


    /**
     * @param $controller
     * @param $link
     *
     * @return Page
     */
    public function findByControllerLink($controller, $link)
    {
        if (isset($this->_controller_link[$controller][$link])) {
            return $this->_controller_link[$controller][$link];
        }
        /** @var $page Page */
        foreach ($this->getAll() as $page) {
            $this->_controller_link[$controller][$link] = $page;
            if ($link == $page->link && $controller == $page->controller) {
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
            if ($page->alias == $alias && !$page->deleted) {
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
    public function onSaveStart(Model\ModelEvent $event)
    {
        $this->log('triggered: ' . __METHOD__);
        /** @var $obj Page  */
        $obj = $event->getObject();

        // todo Переделать на использование роутера
        $pageId = $this->checkAlias($obj->alias);
        if ( false !== $pageId && $obj->getId() != $pageId ) {
            throw new Exception($this->t('The page with this address already exists'));
        }

        $obj->alias = $obj->alias ? $obj->alias : strtolower(\Sfcms::i18n()->translit(trim($obj->name, '/ ')));
        $obj->path = serialize(array_reverse($this->createPath($obj)));

        // Настраиваем связь с модулями
        // @todo Должно определяться по имени модели, но в странице не указана связанная модель, а только контроллер
        $this->trigger(sprintf('plugin.page-%s.save.start', $obj->controller), $event);
    }

    /**
     * Creating path cache for breadcrumbs
     * @param Page $obj
     *
     * @return array
     */
    protected function createPath(Page $obj)
    {
        $path   = array();
        while (null !== $obj) {
            $path[] = array(
                'id'    => $obj->id,
                'name'  => $obj->name,
                'url'   => $obj->alias,
            );
            $obj = $obj->parent ? $this->find($obj->parent) : null;
        }
        return $path;
    }

    /**
     * Вернет список доступных модулей
     * Нужны для составления списка создания страницы в админке
     * @return array|null
     */
    public function getAvaibleModules()
    {
        if (is_null($this->availableModules)) {
            $locator = new FileLocator(array(
                $this->app()->getContainer()->getParameter('root'),
                $this->app()->getContainer()->getParameter('sf_path')
            ));

            $controllersFile = $locator->locate('app/controllers.xml');
            $content = file_get_contents($controllersFile);

            if (!$content) {
                return array();
            }

            $xmlControllers = new SimpleXMLElement( $content );

            $this->availableModules = array();

            foreach ($xmlControllers->children() as $child) {
                $this->availableModules[(string)$child['name']] = array('label' => (string)$child->label);
            }
        }

        $ret = array();
        foreach ($this->availableModules as $key => $mod) {
            $ret[$key] = $this->app()->getContainer()->get('translator')->trans($mod['label']);
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
        $max = $this->getDB()->fetchOne(
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

    public function getParents()
    {
        if (!$this->parents) {
            $this->createParentsIndex();
        }
        return $this->parents;
    }


    /**
     * Создает список $this->parents по данным из $this->all
     *
     * @return array
     */
    private function createParentsIndex()
    {
        $start = microtime(1);
        $this->parents = array();
        // создаем массив, индексируемый по родителям
        /** @var array $data */
        if (count($this->parents) == 0) {
            foreach ($this->getAll() as $data) {
                $this->parents[$data['parent']][$data['id']] = $data;
            }
        }
        $this->log(__FUNCTION__ . ' (' . round(microtime(1) - $start, 3) . ' sec)');
    }

    /**
     * Вернет массив со списком разделов для указания в форме
     * @param int $parent
     * @param int $level
     * @return array
     */
    public function getSelectOptions( $parent = 0, $level = 0 )
    {
        $return = array('0' => $this->t('No parent'));
        if (!$this->parents) {
            $this->createParentsIndex();
        }
        /** @var $obj Page */
        foreach ($this->parents[$parent] as $obj) {
            $return[$obj->id] = str_repeat('&nbsp;', $level * 4) . $obj->name;
            if (isset($this->parents[$obj->id])) {
                $return += $this->getSelectOptions($obj->id, $level + 1);
            }
        }

        return $return;
    }

    /**
     * Вернет объект формы
     * @return PageForm
     */
    public function getForm()
    {
        if (!isset( $this->form )) {
            $this->form = new PageForm();
            $this->form->getChild('controller')->setVariants($this->getAvaibleModules());
            $this->form->getChild('protected')->setVariants($this->getModel('User')->getGroups());
        }
        return $this->form;
    }
}
