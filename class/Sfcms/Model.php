<?php
namespace Sfcms;

use App;
use function array_keys;
use Doctrine\DBAL;
use function get_class;
use Module\User\Object\User;
use function reset;
use function returnArgument;
use RuntimeException;
use Sfcms\Data\AbstractDataField;
use Sfcms\Data\Collection;
use Sfcms\Data\DataManager;
use Sfcms\Data\Object as DomainObject;
use Sfcms\Data\Query\Builder as QueryBuilder;
use Sfcms\Data\Relation;
use Sfcms\Data\Watcher;
use Sfcms\Db\Criteria;
use Sfcms\Model\ModelEvent;
use Sfcms\Data\Field;

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

    private $dataManager;

    /**
     * Creating model
     *
     * @param DataManager $dataManager
     * @throws \ErrorException
     */
    final public function __construct(DataManager $dataManager)
    {
        $alias = $this->eventAlias();
        if (method_exists($this, 'onSaveStart')) {
            $this->on("{$alias}.save.start", [get_class($this), 'onSaveStart']);
        }
        if (method_exists($this, 'onSaveSuccess')) {
            $this->on("{$alias}.save.success", [get_class($this), 'onSaveSuccess']);
        }
        $this->init();
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
    static public function getDB()
    {
        try {
            return App::cms()->getContainer()->get('db');
        } catch (\PDOException $e) {
            static::app()->getLogger()->alert($e->getMessage());
            die($e->getMessage());
        }
    }

    /**
     * @return DBAL\Connection
     */
    public function getDBAL()
    {
        return $this->app()->getContainer()->get('doctrine.connection');
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
        return \App::cms()->getDataManager()->getModel($model);
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
     * @param array $params
     *
     * @return DomainObject
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
     * @return int
     * @throws Data\Exception
     * @throws \ErrorException
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
            $this->trigger($this->eventAlias() . '.save.start', $event);
        }

        $fields = call_user_func(array($this->objectClass(), 'fields'));
        $ids = $obj->pkValues();
        $saveData = [];
        $saveDataTypes = [];

        /** @var AbstractDataField $field */
        foreach ($fields as $field) {
            $val = $obj->get($field->getName());
            if ( $state !== DomainObject::STATE_DIRTY ||
                ($state === DomainObject::STATE_DIRTY && $obj->isChanged($field->getName()))
            ) {
                $saveData[$field->getName()] = $val;
                switch (get_class($field)) {
                    case Field\IntField::class:
                    case Field\TinyintField::class:
                        $saveDataTypes[$field->getName()] = PDO::PARAM_INT;
                        $saveData[$field->getName()] = (int) $val;
                        break;
                    case Field\DatetimeField::class:
                        if ($val instanceof \DateTime) {
                            $saveData[$field->getName()] = $val->format('Y-m-d H:i:s');
                            $saveDataTypes[$field->getName()] = PDO::PARAM_STR;
                        } else {
                            $saveDataTypes[$field->getName()] = PDO::PARAM_INT;
                        }
                        break;
                    case Field\DecimalField::class:
                    case Field\TextField::class:
                    case Field\BlobField::class:
                    case Field\VarcharField::class:
                    default:
                        $saveDataTypes[$field->getName()] = PDO::PARAM_STR;
                }
            }
        }

        // Nothing to save
        if (0 == count($saveData)) {
            return true;
        }

        $preparedKeysData = $this->getPreparedKeysData($saveData);

        if (!$forceInsert && !in_array(null, $ids, true) && DomainObject::STATE_DIRTY == $state) {
            // UPDATE
            $ret = $this->getDBAL()->update($this->getTable(), $preparedKeysData, $ids, $saveDataTypes);
        } else {
            // INSERT
            if (count($ids) == 1) {
                $idField = array_keys($ids)[0];
                $this->app()->getLogger()->debug('ids', ['ids' => $ids]);
                /** @var $field AbstractDataField */
                $field   = $obj->field($idField);
                $fieldValue = $obj->get($idField);
                if ($field->isAutoIncrement() && (null === $fieldValue || '' === $fieldValue)) {
                    unset($saveData[$idField]);
                }
                if ($field->isAutoIncrement() && $obj->get($idField)) {
                    return false;
                }
            }
            $ret = $this->getDBAL()->insert($this->getTable(), $preparedKeysData, $saveDataTypes);
            if ($ret) {
                $obj->setId($this->getDBAL()->lastInsertId());
            }
            $this->addToMap($obj);
        }
        if ($ret > 0) {
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
     * @param array $saveData
     * @return array
     */
    private function getPreparedKeysData(array $saveData)
    {
        $preparedKeysData = [];
        $platform = $this->getDBAL()->getDatabasePlatform();
        if ($platform instanceof DBAL\Platforms\MySqlPlatform) {
            foreach ($saveData as $key => $val) {
                $preparedKeysData["`$key`"] = $val;
            }
        } else {
            $preparedKeysData = $saveData;
        }

        return $preparedKeysData;
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
