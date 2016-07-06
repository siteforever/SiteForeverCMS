<?php
namespace Sfcms;

use App;
use Doctrine\DBAL;
use Sfcms\Data\AbstractField;
use Sfcms\Data\DataManager;
use Sfcms\Data\Object as DomainObject;
use Sfcms\Data\Collection;
use Sfcms\Data\Query\Builder as QueryBuilder;
use Sfcms\Model\ModelEvent;
use Sfcms\db;
use Sfcms\Db\Criteria;
use RuntimeException;
use Module\User\Object\User;
use Sfcms\Data\Watcher;
use Sfcms\Data\Field;
use Sfcms\Data\Relation;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Model interface
 */
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
     * @var db
     */
    protected $db;

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
     * Количество relation полей, которые должны быть загружены группой
     * @var array
     */
    protected $with = array();

    /**
     * Config options
     * @var array
     */
    protected $config = array();

    /** @var \Sfcms\Data\DataManager */
    protected $dataManager;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * Creating model
     *
     * @param DataManager $dataManager
     */
    final public function __construct(DataManager $dataManager)
    {
        $alias = $this->eventAlias();
        $this->dataManager = $dataManager;
        $this->eventDispatcher = $dataManager->getEventDispatcher();

        if (method_exists($this, 'onSaveStart')) {
            $this->on(sprintf('%s.save.start', $alias), [get_class($this), 'onSaveStart']);
        }
        if (method_exists($this, 'onSaveSuccess')) {
            $this->on(sprintf('%s.save.success', $alias), [get_class($this), 'onSaveSuccess']);
        }
        $this->init();
    }

    /**
     * Dispatch named event
     *
     * @param string $eventName
     * @param ModelEvent $event
     *
     * @return ModelEvent
     */
    public function trigger($eventName, ModelEvent $event)
    {
        return $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * @param $eventName
     * @param $callback
     * @param $priority
     *
     * @throws RuntimeException
     */
    public function on($eventName, $callback, $priority = 0)
    {
        if (!(is_array($callback) || $callback instanceof \Closure)) {
            throw new RuntimeException('"$callback" must be Array or Closure');
        }
        $this->eventDispatcher->addListener($eventName, $callback, $priority);
    }

    /**
     * @return mixed
     */
    public function getDataManager()
    {
        return $this->dataManager;
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
     * @static
     * @deprecated since version 0.7, will be removed on version 0.8
     * @return db
     */
    public function getDB()
    {
        return $this->dataManager->getDB();
    }

    /**
     * @return DBAL\Connection
     */
    public function getDBAL()
    {
        return $this->app()->getContainer()->get('database_connection');
    }

    /**
     * initialisation
     * @return void
     */
    protected function init()
    {
    }

    /**
     * Relationship with other models
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
     * Вернет нужную модель
     *
     * @param  $model
     *
     * @return Model
     * @throws RuntimeException
     */
    final public function getModel($model)
    {
        return $this->dataManager->getModel($model);
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
        }

        return (string) $this->table;
    }


    /**
     * Событие возникает при создании новой таблицы
     * @deprecated
     * @return void
     */
    protected function onCreateTable()
    {
        // todo Реализовать через event.manager
        // todo Возможно, в качестве фикстур
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
    public function find($crit, $params = array())
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
            throw new RuntimeException('Criteria for '.__METHOD__.' is not valid ');
        }

        if (null === $query && $crit) {
            $query = new QueryBuilder($this, $crit);
        }

        $sql  = $query->getSQL();
        $data = $this->getDB()->fetch($sql, db::F_ASSOC, $crit->params);

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
    protected function findBy($criteria)
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

        $raw = $this->getDB()->fetchAll($query->getSQL());

        if ($raw) {
            $collection = new Collection($raw, $this);

            if (count($with)) {
                foreach ($with as $rel) {
                    $relation = $this->getRelation($rel, $collection->getRow(0));
                    $relation->with($collection, $rel);
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
     * Saving object data into table
     * @param DomainObject $obj object for saving
     * @param bool $forceInsert Force inserted data in table
     * @param bool $silent Not triggered save events
     *
     * @return bool|int
     */
    public function save(DomainObject $obj, $forceInsert = false, $silent = false)
    {
        if ($obj->isStateSaving()) { // Защита от замкнутого сохранения из-за событий
            return false;
        }
        $state = $obj->state();
        $obj->markSaving();

        $event = new Model\ModelEvent($obj, $this);
        if (!$silent) {
            $this->trigger('save.start', $event);
            $this->trigger(sprintf('%s.save.start', $this->eventAlias()), $event);
        }

        $fields = call_user_func(array($this->objectClass(), 'fields'));
        $id = $obj->pkValues();
        $idKeys = array_keys($id);
        $saveData = array();

        /** @var AbstractField $field */
        foreach ($fields as $field) {
            $val = $obj->get($field->getName());
            if ($state !== DomainObject::STATE_DIRTY
                || ($state === DomainObject::STATE_DIRTY && $obj->isChanged($field->getName()))
            ) {
                if ($val instanceof \DateTime) {
                    $val = $val->format('Y-m-d H:i:s');
                }
                $saveData[$field->getName()] = $val;
            }
        }

        // Nothing to save
        if (0 == count($saveData)) {
            return true;
        }

        $ret = false;
        if (!$forceInsert && !in_array(null, $id, true) && DomainObject::STATE_DIRTY == $state) {
            // UPDATE
            $where = array_map(function ($key, $val) {
                return "`{$key}` = '{$val}'";
            }, $idKeys, $id);
            $ret = $this->getDB()->update($this->getTable(), $saveData, join(' AND ', $where));
        } else {
            // INSERT
            if (count($id) == 1) {
                /** @var $field AbstractField */
                $field   = $obj->field($idKeys[0]);
                $fieldValue = $obj->get($idKeys[0]);
                if ($field->isAutoIncrement() && (null === $fieldValue || '' === $fieldValue)) {
                    unset($saveData[$idKeys[0]]);
                }
                if ($field->isAutoIncrement() && $obj->get($idKeys[0])) {
                    return false;
                }
            }
            $ret = $this->getDB()->insert($this->getTable(), $saveData);
            if ($ret && 1 == count($id)) {
                $obj->setId($ret);
            }
            $this->addToMap($obj);
        }
        if (false !== $ret) {
            if (!$silent) {
                $this->trigger('save.success', $event);
                $this->trigger(sprintf('%s.save.success', $this->eventAlias()), $event);
            }
            if ($obj->isStateSaving()) {
                $obj->markClean();
            }
        }

        return $ret;
    }

    /**
     * Тут нельзя вызывать сохраниение объекта, или вызывать очень осторожно.
     * Иначе возникнет бесконечный цикл
     *
     * @param ModelEvent $event
     */
    public static function onSaveSuccess(Model\ModelEvent $event)
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

        if (!$this->onDeleteStart($id)) {
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
     * @deprecated in 0.6 Was deleted in version 0.8
     * @return boolean
     */
    public function onDeleteStart($id = null)
    {
        // @todo Delete in v0.8
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

        $count = $this->getDB()->fetchOne($sql);

        if ($count) {
            return $count;
        }

        return 0;
    }

    /**
     * Вернет количество записей по условию
     * @param string|Criteria $cond
     * @param array           $params
     *
     * @return int
     */
    final public function sum($column, $cond = '', $params = array())
    {
        //$this->log( $cond, 'count' );
        if (is_object($cond) && $cond instanceof Criteria) {
            $params = $cond->params;
            $cond   = $cond->condition;
        }
        $criteria = new QueryBuilder($this, array(
            'select' => sprintf('SUM(`%s`)', $column),
            'cond'   => $cond,
            'params' => $params,
        ));
        $sql = $criteria->getSQL();
        $sum = $this->getDB()->fetchOne($sql);
        if ($sum) {
            return $sum;
        }

        return 0;
    }

    /**
     * @param array $params
     * @return QueryBuilder
     */
    public function getQueryBuilder($params = array())
    {
        return new QueryBuilder($this, $params);
    }

    /**
     * @return DBAL\Query\QueryBuilder
     */
    public function dbalQueryBuilder()
    {
        $qb = $this->getDBAL()->createQueryBuilder();
        $qb->from($this->getTable(), 't');

        return $qb;
    }

    /**
     * Начало транзакции
     * @return void
     */
    public function transaction()
    {
        $this->getDBAL()->beginTransaction();
    }

    /**
     * Применение транзакции
     * @return void
     */
    public function commit()
    {
        $pdo = $this->getDB()->getResource();
        $pdo->commit();
    }

    /**
     * Откат транзакции
     * @return void
     */
    public function rollBack()
    {
        $pdo = $this->getDB()->getResource();
        $pdo->rollBack();
    }

}
