<?php
namespace Sfcms;

/**
 * Интерфейс модели
 */

use App;
use Sfcms\Data\Object as DomainObject;
use Module\System\Model\LogModel;
use Sfcms\Component;
use Sfcms\Data\Collection;
use Sfcms\Data\Query\Builder as QueryBuilder;
use Sfcms\Model\ModelEvent;
use Sfcms\db;
use Sfcms\Db\Criteria;
use RuntimeException;
use PDO;
use Module\User\Object\User;
use Sfcms\Data\Table;
use Sfcms\Form\Form;
use Sfcms\Data\Watcher;
use Sfcms\Data\Field;
use Sfcms\Data\Relation;
use Symfony\Component\EventDispatcher\Event;

abstract class Model extends Component
{
    const HAS_MANY = 'has_many'; // содержет много
    const ONE_TO_MANY = 'has_many'; // содержет много
    const HAS_ONE = 'has_one'; // содержет один
    const ONE_TO_ONE = 'has_one'; // содержет один
    const BELONGS = 'belongs'; // принадлежит
    const MANY_TO_ONE = 'belongs'; // принадлежит

    const STAT = 'stat'; // статистическая связь

    /**
     * Тип таблицы
     * @var string
     */
    protected $engine = 'InnoDB'; // 'MyISAM';

    /**
     * @var db
     */
    protected $db;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var DomainObject
     */
    protected $data;

    /**
     * Список полей
     * @var array
     */
    protected $fields = array();

    /**
     * Список инстанцированных классов модели
     * @var array
     */
    protected static $all_class = array();

    protected static $exists_tables;

    protected static $dao;

    /** @var array Список моделей, доступных в системе */
    protected static $models = null;

    /**
     * Количество relation полей, которые должны быть загружены группой
     * @var array
     */
    protected $with = array();

    /**
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Кеш запросов для текущей модели
     * Кешируются запросы вида field = value или field IN (values)
     * @var array
     */
    private $_queries_cache = array();

    /**
     * Создание модели
     */
    final private function __construct()
    {
        $this->request = $this->app()->getRequest();
        $this->config  = $this->app()->getConfig();

        // база данных
        // определяется только в моделях
        if (is_null(static::$dao)) {
            static::$dao = db::getInstance($this->config->get('db'));
            static::$dao->setLoggerClass($this->app()->getLogger());
        }
        $this->db  = static::$dao;
        $this->pdo = $this->db->getResource();

        if (method_exists($this, 'onSaveStart')) {
            $this->on('save.start', array($this, 'onSaveStart'));
        }
        if (method_exists($this, 'onSaveSuccess')) {
            $this->on('save.success', array($this, 'onSaveSuccess'));
        }
        $this->init();
    }

    /**
     * @static
     * @return db
     */
    static public function getDB()
    {
        return static::$dao;
    }

    /**
     * Инициализация
     * @return void
     */
    protected function init()
    {
    }

    /**
     * Отношения модели с другими моделями
     *
     * <p>Пример:</p>
     * <pre>array(
     *     'Category' => array(self::BELONGS, 'CategoryModel', 'category_id', 'order'=>'date', 'limit'=>20),
     * );</pre>
     * <ul>
     *     <li>Первый параметр - тип отношения</li>
     *     <li>Второй параметр - модель, с помощью которой будет происходить поиск</li>
     *     <li>Третий параметр - ключ, по которому будет происходить поиск</li>
     *     <li>order и limit   - Дополнительные параметры</li>
     * </ul>
     * @return array
     */
    public function relation()
    {
        return array();
    }

    /**
     * Проверяет существование таблицы
     * @param string $table
     *
     * @return boolean
     */
    private function isExistTable($table)
    {
        if (!isset(self::$exists_tables)) {
            self::$exists_tables = array();
            $tables = $this->db->fetchAll("SHOW TABLES", false, DB::F_ARRAY);
            foreach ($tables as $t) {
                self::$exists_tables[] = $t[0];
            }
        }

        return in_array($table, self::$exists_tables);
    }

    /**
     * Добавит созданную таблицу в кэш
     * @param string $table
     *
     * @return void
     */
    private function addNewTable($table)
    {
        $this->getDB()->query($this->getCreateTable());
        self::$exists_tables[] = $table;
    }

    /**
     * Построение запроса для создания таблицы
     *
     * @return string
     * @throws Exception
     */
    public function getCreateTable()
    {
        $result = array(sprintf("CREATE TABLE `%s` (\n\t", $this->getTable()));

        $object_class = $this->objectClass();
        $fields = call_user_func(array($object_class, 'fields'));
        $pk     = call_user_func(array($object_class, 'pk'));

        $params = array_map(function ($field) {
            /** @var Field $field */
            return $field->toString();
        }, $fields);

        if ($pk) {
            if (is_array($pk)) {
                $pk = '`' . join('`,`', $pk) . '`';
            } else {
                $pk = "`" . str_replace(',', '`,`', $pk) . "`";
            }
            $params[] = "PRIMARY KEY ({$pk})";
        }

        foreach (call_user_func(array($object_class, 'keys')) as $key => $val) {
            $found = false;
            if (is_array($val)) {
                foreach ($val as $v) {
                    $subfound = false;
                    foreach ($fields as $field) {
                        /** @var $field Field */
                        if ($field->getName() == $v) {
                            $subfound = true;
                            break;
                        }
                    }
                    $found = $found || $subfound;
                }
                $val = implode(',', $val);
            } else {
                foreach ($fields as $field) {
                    /** @var $field Field */
                    if ($field->getName() == $val) {
                        $found = true;
                        break;
                    }
                }
            }

            if (!$found) {
                //die('Key column doesn`t exist in table');
                throw new Exception("Key column '{$val}' doesn`t exist in table");
            }

            $val = str_replace(',', '`,`', $val);
            if (is_numeric($key)) {
                $key = $val;
            }
            $params[] = "KEY `{$key}` (`{$val}`)";
        }

        $result[] = join(",\n\t", $params) . "\n";

        $result[] = ") ENGINE={$this->engine} DEFAULT CHARSET=utf8";

        return join("\n", $result);
    }

    /**
     * Вернет нужную модель
     * @static
     *
     * @param  $model
     *
     * @return self
     * @throws RuntimeException
     */
    final static public function getModel($model)
    {
        $class_name = $model;
        // Если нет в кэше и указан не абсолютный путь
        if (!isset(self::$all_class[$model]) && false === strpos($class_name, '\\')) {
            // Если указан псевдоним
            // Псевдонимом считается класс, не имеющий символов \ и _
            if (null === self::$models) {
                self::$models = App::getInstance()->getModels();
            }
            $modelKey = strtolower($class_name);
            if (isset(self::$models[$modelKey])) {
                $class_name = self::$models[$modelKey];
            }
        }
        if (!isset(self::$all_class[$model])) {
            if (class_exists($class_name, true)) {
                self::$all_class[$model] = new $class_name();
            } else {
                throw new RuntimeException(sprintf('Model "%s" not found', $class_name));
            }
        }

        return self::$all_class[$model];
    }

    /**
     * Создать объект
     * @param array $data   Массив инициализации объекта
     * @param bool  $reFill Принудительно записать поля, если создается объект из массива, име.щего id
     *
     * @return DomainObject
     */
    final public function createObject($data = array(), $reFill = false)
    {
        $class_name = $this->objectClass();
        $pk = call_user_func(array($class_name, 'pkAsArray'));
        $id = array();
        foreach ($pk as $pri) {
            $id[$pri] = isset($data[$pri]) ? $data[$pri] : null;
        }
        if (!in_array(null, $id, true)) {
            $obj = $this->getFromMap($id);
            if ($obj) {
                if ($reFill) {
                    $obj->attributes = $data;
                }
                return $obj;
            }
        }
        /** @var $obj DomainObject */
        $obj = new $class_name($this, $data);
        if (!in_array(null, $id, true)) {
            $this->addToMap($obj);
        }

        return $obj;
    }


    /**
     * Адаптер к наблюдателю для получения объекта
     * @param array $id
     *
     * @return DomainObject
     */
    private function getFromMap($id)
    {
        return Watcher::exists($this->objectClass(), $id);
    }

    /**
     * Адаптер к наблюдателю для добавления объекта
     *
     * @param DomainObject $obj
     */
    private function addToMap(DomainObject $obj)
    {
        Watcher::add($obj);
    }


    /**
     * Класс для сущности доменного объекта
     * @return string
     */
    public function objectClass()
    {
        return str_replace(array('\Model', 'Model'), array('\Object', ''), get_class($this));
    }

    /**
     * Класс таблицы БД
     * @abstract
     * @return string
     */
    public function tableClass()
    {
        return $this->objectClass();
    }

    /**
     * Вернет таблицу модели
     * @return string
     */
    final public function getTable()
    {
        if (null === $this->table) {
            $class = $this->tableClass();

            $this->table = call_user_func(array($class, 'table'));

            if ($this->config->get('db.migration')) {
                if ($this->isExistTable($this->table)) {
                    $this->migration();
                } else {
                    $this->addNewTable($this->table);
                    $this->onCreateTable();
                }
            }
        }

        return $this->table;
    }

    /**
     * Выполнение сверки таблицы и выполнение миграции полей
     * @return void
     */
    private function migration()
    {
        $class = $this->objectClass();
        $sys_fields  = call_user_func(array($class, 'fields'));
        $table       = call_user_func(array($class, 'table'));
        $have_fields = $this->getFields();

        $txtsys_fields = array_map(
            function (Field $field) {
                return $field->getName();
            },
            $sys_fields
        );

        $add_array = array_diff($txtsys_fields, $have_fields);
        $del_array = array_diff($have_fields, $txtsys_fields);

        $sql = array();

        if (count($add_array) || count($del_array)) {
            foreach ($del_array as $col) {
                $sql[] = "ALTER TABLE `{$table}` DROP COLUMN `$col`";
            }
            foreach ($add_array as $key => $col) {
                $after = '';
                if ($key == 0) {
                    $after = ' FIRST';
                }
                if ($key > 0) {
                    $after = ' AFTER `' . $sys_fields[$key - 1]->getName() . '`';
                }
                $sql[] = "ALTER TABLE `{$this->getTable()}` ADD COLUMN " . $sys_fields[$key] . $after;
            }

            foreach ($sql as $query) {
                $this->getDB()->query($query);
            }
        }
    }

    /**
     * Вернет список полей
     *
     * @param string $table
     *
     * @return array
     * @throws \ErrorException
     */
    protected function getFields()
    {
        $table = $this->getTable();
        $start = microtime(true);
        $pdo   = $this->getDB()->getResource();

        $result = $pdo->prepare("SHOW COLUMNS FROM `$table`");

        $fields = array();

        if (!$result->execute()) {
            throw new \ErrorException('Result Fields Query not valid');
        }

        foreach ($result->fetchAll(PDO::FETCH_OBJ) as $field) {
            $fields[] = $field->Field;
        }

        $exec = round(microtime(true) - $start, 4);
        $this->log("SHOW COLUMNS FROM `$table`" . " [$exec сек]", 'SQL');

        return $fields;
    }

    /**
     * Событие возникает при создании новой таблицы
     * @return void
     */
    protected function onCreateTable()
    {
    }

    /**
     * Установить связи для следующего запроса
     * @return Model
     */
    public function with()
    {
        if (1 == func_num_args() && func_get_arg(0) && is_array(func_get_arg(0))) {
            $this->with = func_get_arg(0);
        }
        if (func_num_args() >= 1 && is_string(func_get_arg(0))) {
            $this->with = func_get_args();
        }

        return $this;
    }

    /**
     * Create criteria
     *
     * @param array $params
     *
     * @return Criteria
     */
    public function createCriteria($params = array())
    {
        return new Criteria($params);
    }

    /**
     * @param array $data
     *
     * @return Collection
     */
    public function createCollection(array $data = null)
    {
        return new Collection($data, $this);
    }

    /**
     * Finding data by primary key
     *
     * @param $pk
     *
     * @return DomainObject
     */
    final public function findByPk($pk)
    {
        $class = $this->objectClass();
        $pkFields = call_user_func(array($class, 'pkAsArray'));
        if (!is_array($pk)) {
            $pk = array($pk);
        }
        $pk = array_combine($pkFields, $pk);
        $obj = $this->getFromMap($pk);
        if ($obj) {
            return $obj;
        }
        $cond = array();
        $params = array();
        foreach ($pk as $pri => $value) {
            $cond[] = "`{$pri}` = :{$pri}";
            $params[':'.$pri] = $value;
        }

        $crit = $this->createCriteria();
        $crit->condition = join(' AND ', $cond);
        $crit->params = $params;
        $crit->limit = 1;

        return $this->find($crit);
    }

    /**
     * Finding data by criteria
     *
     * @param int|array|string|Criteria $crit
     * @param array                     $params
     *
     * @return DomainObject
     * @throws Exception
     */
    final public function find($crit, $params = array())
    {
        $query = null;
        if (is_object($crit)) {
            if ($crit instanceof Criteria) {
                $query = new QueryBuilder($this, $crit);
            } elseif ($crit instanceof QueryBuilder) {
                $query = $crit;
            }
        }
        // не определился критерий, но параметр - число
        // тогда полагаем, что параметр - это ID объекта
        if (null === $query && is_numeric($crit)) {
            return $this->findByPk($crit);
        } elseif (is_array($crit)) {
            $default = array(
                'select' => '*',
                'cond'   => '',
                'params' => array(),
                'limit'  => 1,
            );
            $crit = array_merge($default, $crit);
        } elseif (is_string($crit)) {
            $crit = array(
                'cond'   => $crit,
                'params' => $params,
                'limit'  => 1,
            );
        }

        if (is_array($crit)) {
            $crit = $this->createCriteria($crit);
        }

        if (!(is_object($crit) || $crit instanceof Criteria)) {
            throw new Exception('Not valid criteria');
        }

        if (null === $query && $crit) {
            $query = new QueryBuilder($this, $crit);
        }

        $sql  = $query->getSQL();
        $data = $this->db->fetch($sql, db::F_ASSOC, $crit->params);

        if ($data) {
            $obj = $this->createObject($data);
            if ($obj->isStateCreate()) {
                $obj->markClean();
            }
            return $obj;
        }

        return null;
    }

    /**
     * Find object by array params
     *
     * <code>
     * $params = array('field1' => 'value1', 'field2' => 'value2');
     * </code>
     *
     * @param $criteria
     *
     * @not_tested
     *
     * @return DomainObject
     */
    public function findBy($criteria)
    {
        $keys = $this->getKeys();
        $findKeys = array_keys($criteria);
        foreach ($keys as $name => $index) {
            if ($index == $findKeys) {
                $obj = Watcher::existsByIndex($this->objectClass(), array($name => $criteria));
            }
        }
        $cond = array();
        $params = array();
        foreach ($criteria as $key => $value) {
            $cond[$key] = $value;
        }
        $c = $this->createCriteria();
        $c->condition = $cond;
        $c->params = $params;
        $c->limit = 1;
        return $this->find($c);
    }

    /**
     * @return mixed
     */
    public function getKeys()
    {
        return call_user_func(array($this->objectClass(), 'keys'));
    }

    /**
     * @param array|string $crit
     * @param array        $params
     * @param string       $order
     * @param string       $limit
     *
     * @return array|Collection
     * @throws Exception
     */
    final public function findAll($crit = array(), $params = array(), $order = '', $limit = '')
    {
        $with       = $this->with;
        $this->with = array();

        $cache_match = null;
        // ==== CACHING ====
        if ($crit instanceof Criteria) {
            if (preg_match('@^`(\w+)`\s(IN|=)\s\(?(.*?)\)?$@', $crit->condition, $cache_match)) {
                if ('=' == $cache_match[2]) {
                    $hash_key = $this->objectClass() . $cache_match[1] . $crit->params[$cache_match[3]];
                    if (isset($this->_queries_cache[$hash_key])) {
                        return $this->_queries_cache[$hash_key];
                    }
                }
            }
        }
        // ==== /CACHING ====

        if (is_array($crit) || (is_object($crit) && $crit instanceof Criteria)) {
            $query = new QueryBuilder($this, $crit);
        } elseif (is_string($crit) && is_array($params) && '' != $crit) {
            $query = new QueryBuilder($this, array(
                'cond'   => $crit,
                'params' => $params,
                'order'  => $order,
                'limit'  => $limit,
            ));
        } elseif (is_object($crit) && $crit instanceof QueryBuilder) {
            $query = $crit;
        } else {
            throw new Exception('Not valid criteria');
        }

        $raw = $this->db->fetchAll($query->getSQL());

        if (count($raw)) {
            $collection = new Collection($raw, $this);
            // ==== CACHING ====
            if ($cache_match && 'IN' == $cache_match[2]) {
                $cache_values = $crit->params[$cache_match[3]];
                if (!is_array($cache_values)) {
                    $cache_values = array($cache_values);
                }
                foreach ($cache_values as $cache_value) {
                    $hash_key = $this->objectClass() . $cache_match[1] . $cache_value;
                    if (!isset($this->_queries_cache[$hash_key])) {
                        $raw_filtered = array_filter(
                            $raw,
                            function ($data) use ($cache_match, $cache_value) {
                                return isset($data[$cache_match[1]]) && $data[$cache_match[1]] == $cache_value;
                            }
                        );
                        $this->_queries_cache[$hash_key] = new Collection($raw_filtered, $this);
                    }
                }
            }
            // ==== /CACHING ====
            if (count($with)) {
                foreach ($with as $rel) {
                    $relation = $this->getRelation($rel, $collection->getRow(0));
                    $relation->with($collection);
                }
            }
        } else {
            $collection = new Collection();
        }

        return $collection;
    }

    /**
     * Поиск по отношению
     * @param string $rel
     * @param mixed  $obj
     *
     * @return array|DomainObject|null
     */
    final public function findByRelation($rel, DomainObject $obj)
    {
        $relation = $this->getRelation($rel, $obj);
        if ($relation) {
            return $relation->find();
        }

        return null;
    }


    /**
     * Фабрика отношений
     * @param string $rel
     * @param DomainObject $obj
     *
     * @return null|Relation
     * @throws \InvalidArgumentException
     */
    private function getRelation($rel, DomainObject $obj)
    {
        $relation = $this->relation();

        if (!is_string($rel)) {
            throw new \InvalidArgumentException('Argument `rel` is not a string');
        }

        switch ($relation[$rel][0]) {
            case self::BELONGS:
                return new Relation\Belongs($rel, $obj);
            case self::HAS_ONE:
                return new Relation\One($rel, $obj);
            case self::HAS_MANY:
                return new Relation\Many($rel, $obj);
            case self::STAT:
                return new Relation\Stat($rel, $obj);
        }

        return null;
    }

    /**
     * Генерирует псевдоним для события
     * @return string
     * @throws \ErrorException
     */
    public function eventAlias()
    {
        if (preg_match('/(\w+)model$/i', get_class($this), $match)) {
            return strtolower($match[1]);
        }
        throw new \ErrorException("Can not define event alias.\nYou need redefine \"eventAlias()\" method.");
    }

    /**
     * Сохраняет данные модели в базе
     * @param DomainObject $obj
     * @param bool   $forceInsert
     *
     * @return bool|int
     */
    public function save(DomainObject $obj, $forceInsert = false)
    {
        $event = new Model\ModelEvent($obj, $this);
        $this->trigger('save.start', $event);
        $this->trigger(sprintf('%s.save.start', $this->eventAlias()), $event);

        $fields = call_user_func(array($this->objectClass(), 'fields'));
        $id = $obj->pkValues();
        $id_keys = array_keys($id);
        $save_data = array();
        /** @var Field $field */
        foreach ($fields as $field) {
            $val = $obj->get($field->getName());
//            if (!isset($id[$field->getName()]) && null !== $val) {
            if (!$obj->isStateDirty() || ($obj->isStateDirty() && $obj->isChanged($field->getName()))) {
                $save_data[$field->getName()] = $val;
            }
//            }
        }

        // Nothing to save
        if (0 == count($save_data)) {
            return true;
        }

        $ret = false;
        if (!$forceInsert && !in_array(null, $id, true) && $obj->isStateDirty()) {
            $where = array_map(function ($key, $val) {
                return "`{$key}` = '{$val}'";
            }, $id_keys, $id);
            $ret = $this->db->update($this->getTable(), $save_data, join(' AND ', $where));
        } else {
            if (count($id) == 1) {
                /** @var $field Field */
                $field   = $obj->field($id_keys[0]);
                if (!($field->isAutoIncrement() && !$obj->get($id_keys[0]))) {
                    return false;
                }
            }
            $ret = $this->db->insert($this->getTable(), $save_data);
            if ($ret && 1 == count($id)) {
                $obj->setId($ret);
            }
            $this->addToMap($obj);
        }
        if (false !== $ret) {
            $this->trigger('save.success', $event);
            $this->trigger(sprintf('%s.save.success', $this->eventAlias()), $event);
            $obj->markClean();
        }

        return $ret;
    }

    /**
     * Тут нельзя вызывать сохраниение объекта, или вызывать очень осторожно.
     * Иначе возникнет бесконечный цикл
     *
     * @param ModelEvent $event
     */
    public function onSaveSuccess(Model\ModelEvent $event)
    {
        // Никогда не вызовется
        // Для вызова надо переоределить в модели
    }

    /**
     * Удаляет строку из таблицы
     * @param int $id
     *
     * @return boolean|mixed
     */
    final public function delete($id)
    {
        $obj = $this->find($id);
        if (!$obj) {
            return false;
        }
        $event = new ModelEvent($obj, $this);

        if ($this->onDeleteStart($id) === false) {
            return false;
        }
        $this->trigger('delete.start', $event);
        if (!$event->getContinue()) {
            return false;
        }
        $this->trigger(sprintf('%s.delete.start', $this->eventAlias()), $event);
        if (!$event->getContinue()) {
            return false;
        }

        if ($obj) {
            if ($this->getDB()->delete($this->getTable(), '`id` = :id', array(':id' => $obj->getId()))) {
                Watcher::del($obj);
                $this->trigger('delete.success', $event);
                $this->trigger(sprintf('%s.delete.success', $this->eventAlias()), $event);
                return true;
            }
        }
    }

    /**
     * Событие, вызывается перед удалением объекта
     * Если вернет false, объект не будет удален
     *
     * @param int $id
     *
     * @return boolean
     */
    public function onDeleteStart($id = null)
    {
        return true;
    }

    /**
     * Вернет количество записей по условию
     * @param string|Criteria $cond
     * @param array           $params
     *
     * @return int
     */
    final public function count($cond = '', $params = array())
    {
        //$this->log( $cond, 'count' );
        if (is_object($cond) && $cond instanceof Criteria) {
            $params = $cond->params;
            $cond   = $cond->condition;
        }

        $criteria = new QueryBuilder($this, array(
            'select' => 'COUNT(`id`)',
            'cond'   => $cond,
            'params' => $params,
        ));

        $sql = $criteria->getSQL();

        $count = $this->db->fetchOne($sql);

        if ($count) {
            return $count;
        }

        return 0;
    }

    /**
     * Начало транзакции
     * @return void
     */
    public function transaction()
    {
        $pdo = $this->db->getResource();
        $pdo->beginTransaction();
    }

    /**
     * Применение транзакции
     * @return void
     */
    public function commit()
    {
        $pdo = $this->db->getResource();
        $pdo->commit();
    }

    /**
     * Откат транзакции
     * @return void
     */
    public function rollBack()
    {
        $pdo = $this->db->getResource();
        $pdo->rollBack();
    }

}
