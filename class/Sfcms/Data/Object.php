<?php
/**
 * Интервейс контейнера для данных
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
namespace Sfcms\Data;

use Sfcms\Component;
use Sfcms\Data\Field\Datetime;
use Sfcms\Model;

abstract class Object extends Table
{
    /**
     * @var Model
     */
    protected $model = null;

    /**
     * @var Table
     */
    protected $table = null;

    /**
     * @var array|null
     */
    protected $relation = null;

    /**
     * Список полей, которые были изменены
     * @var array
     */
    protected $changed = array();

    const STATE_CREATE = 0;
    const STATE_CLEAN  = 10;
    const STATE_NEW    = 20;
    const STATE_DIRTY  = 30;
    const STATE_DELETE = 40;
    const STATE_SAVING = 50;

    protected $state;

    /**
     * @param Model $model
     * @param array $data
     */
    public function __construct(Model $model, $data = array())
    {
        $this->state    = self::STATE_CREATE;
        $this->changed  = array();
        $this->model    = $model;
        $this->table    = $model->getTable();
        $this->relation = $this->model->relation();
        $this->data = $data;
    }

    /**
     * Вернет список измененных полей
     * @return array
     */
    public function changedFields()
    {
        return $this->changed;
    }

    /**
     * Was changed field $name
     * @param string $name
     *
     * @return bool
     */
    public function isChanged($name)
    {
        if (isset($this->changed[$name])) {
            return true;
        }
        return false;
    }

    /**
     * @param $key
     *
     * @return array|Object|mixed|null
     */
    public function get($key)
    {
        if (isset($this->relation[$key]) && !isset($this->data[$key])) {
            return $this->model->findByRelation($key, $this);
        }

        return parent::get($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|Component
     * @throws \UnexpectedValueException
     */
    public function set($key, $value)
    {
        if (isset($this->relation[$key])) {
            $this->data[$key] = $value;
            return $this;
        }

        $pk = static::pkAsArray();
        if (in_array($key, $pk) && $this->$key && $this->$key != $value) {
            throw new \UnexpectedValueException('Changing pk is not allowed');
        }
        $oldValue = null;
        if ('attributes' != $key) {
            $oldValue = $this->$key;
        }

        $field = $this->field($key);
        if ($field && $field instanceof Datetime && !$value instanceof \DateTime) {
            $value = new \DateTime($value);
        }

        parent::set($key, $value);
        if ('attributes' != $key && $oldValue != $value) {
            $this->changed[$key] = true;
            if ($this->isStateClean()) {
                $this->markDirty();
            }
        }

        return $this;
    }

    /**
     * @param $name
     *
     * @return void
     */
    public function __unset($name)
    {
        parent::__unset($name);
        if ($this->isStateClean()) {
            $this->markDirty();
        }
    }

    /**
     * @return void
     */
    public function __clone()
    {
        foreach (static::pkAsArray() as $pri) {
            $this->data[$pri] = null;
        }
        $this->markNew();
    }

    /**
     * Returns primary key fields as array items
     *
     * @return array
     */
    public static function pkAsArray()
    {
        $pk = static::pk();
        if (!is_array($pk)) {
            $pk = array($pk);
        }

        return $pk;
    }

    /**
     * Returns primary keys values
     *
     * @return array
     */
    public function pkValues()
    {
        $result = array();
        foreach (static::pkAsArray() as $pri) {
            $result[$pri] = $this->$pri;
        }

        return $result;
    }

    /**
     * Установить id
     * <p>Принимается primary key как в качестве скаляра, так и в виде массива.</p>
     *
     * <ul>
     * <li>Если используется скаляр, и у таблицы БД 1 pk, то будет присвоено значение этому pk</li>
     * <li>Если используется массив и у таблицы несколько pk, то будут присвоены значения соответсвенно массиву,
     * возвращаемому методом static::pk()</li>
     * </ul>
     *
     * <p>В случае, если присваивается ид на объект, уже имеющий ид, то будет выброшено исключение.</p>
     *
     * @param int|string|array $id
     *
     * @return Object
     * @throws Exception
     */
    public function setId($id)
    {
        $pk = static::pkAsArray();
        if (1 == count($pk) && !is_array($id)) {
            $pk = $pk[0];
            $pk_val = $this->$pk;
            if (null === $pk_val || "" === $pk_val) {
                $this->$pk = $id;
                return $this;
            }
        } elseif(count($pk) > 0 && is_array($id)) {
            foreach($pk as $i => $key) {
                if (null !== $this->$key) {
                    break;
                }
                $this->$key = $id[$i];
            }
        }
        throw new Exception('Attempting to set an existing object id');
    }

    /**
     * Вернет значение id
     *
     * @return int|array|null
     */
    public function getId()
    {
        return empty($this->data['id']) ? null : $this->data['id'];
    }

    /**
     * Вернет модель данных
     * @param string $model
     *
     * @return Model
     */
    public function getModel($model = '')
    {
        if ('' === $model) {
            return $this->model;
        } else {
            return Model::getModel($model);
        }
    }

    /**
     * Сохранение
     * @param bool $forceInsert
     *
     * @return int
     */
    public function save($forceInsert = false)
    {
        return $this->model->save($this, $forceInsert);
    }

    /**
     * Удалить запись об объекте из базы
     */
    public function delete()
    {
        $this->model->delete($this->getId());
    }

    public function markSaving()
    {
        $this->state = self::STATE_SAVING;
    }

    /**
     * Как новый
     * @return Object
     */
    public function markNew()
    {
        if (!$this->isStateNew()) {
            Watcher::addNew($this);
            $this->state = self::STATE_NEW;
        }
        return $this;
    }

    /**
     * Как удаленный
     * @return Object
     */
    public function markDeleted()
    {
        if (!$this->isStateDelete()) {
            Watcher::addDelete($this);
            $this->state = self::STATE_DELETE;
        }
        return $this;
    }

    /**
     * На обновление
     * @return Object
     */
    public function markDirty()
    {
        if (!$this->isStateDirty()) {
            Watcher::addDirty($this);
            $this->state = self::STATE_DIRTY;
        }
        return $this;
    }

    /**
     * Стереть везде
     * @return Object
     */
    public function markClean()
    {
        if (!$this->isStateClean()) {
            Watcher::addClean($this);
            $this->changed = array();
            $this->state = self::STATE_CLEAN;
        }
        return $this;
    }

    /**
     * Get inner state
     * @return int
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * Check inner state
     * @param $state
     *
     * @return bool
     */
    public function isState($state)
    {
        return $state == $this->state();
    }

    public function isStateDirty()
    {
        return $this->isState(self::STATE_DIRTY);
    }

    public function isStateClean()
    {
        return $this->isState(self::STATE_CLEAN);
    }

    public function isStateNew()
    {
        return $this->isState(self::STATE_NEW);
    }

    public function isStateDelete()
    {
        return $this->isState(self::STATE_DELETE);
    }

    public function isStateCreate()
    {
        return $this->isState(self::STATE_CREATE);
    }

    public function isStateSaving()
    {
        return $this->isState(self::STATE_SAVING);
    }
}
