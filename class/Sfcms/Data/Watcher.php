<?php
/**
 * Наблюдатель за объектами данных
 * @author keltanas
 */
namespace Sfcms\Data;

use Sfcms\Kernel\AbstractKernel;
use Sfcms\Model;
use Sfcms\Data\Object;

class Watcher
{
    private $all = array();
    private $dirty = array();
    private $new = array();
    private $delete = array();

    private $_objects_keys = array();

    /**
     * @var Watcher
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * @static
     * @return Watcher
     */
    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Watcher();
        }

        return self::$instance;
    }

    /**
     * Очистить хранилище
     */
    public function clear()
    {
        $this->all    = array();
        $this->dirty  = array();
        $this->new    = array();
        $this->delete = array();
    }

    /**
     * Определить ключ для объекта
     * @param Object $obj
     *
     * @return string|null
     */
    public function globalKey(Object $obj)
    {
        $id = $obj->pkValues();
        if ($id) {
            return $this->createGlobalKey(get_class($obj), $id);
        }

        return null;
    }

    public function dumpAll()
    {
        $ret = array();
        foreach ($this->all as $key => $obj) {
            $ret[$key] = (string)$obj . ' ' . print_r($obj->attributes, 1);
        }

        return $ret;
    }

    public function dumpDirty()
    {
        $ret = array();
        foreach ($this->dirty as $key => $obj) {
            $ret[$key] = (string)$obj;
        }

        return $ret;
    }

    public function dumpNew()
    {
        $ret = array();
        foreach ($this->new as $key => $obj) {
            $ret[$key] = (string)$obj;
        }

        return $ret;
    }

    /**
     * Создает ключ для объекта
     * @param  $class_name
     * @param  $id
     *
     * @return string
     */
    private function createGlobalKey($class_name, $id)
    {
        if (is_array($id)) {
            $id = join('.', $id);
        }
        return $class_name . '.' . $id;
    }
//
//    private function createIndexKey($obj)
//    {
//
//    }

    /**
     * Добавить объект
     * @static
     *
     * @param Object $obj
     */
    static public function add(Object $obj)
    {
        $inst = self::instance();
        $inst->all[$inst->globalKey($obj)] = $obj;

//        $class = get_class($obj);
//        if (isset($inst->_objects_keys[$class])) {
//            $keys = $inst->_objects_keys[$class];
//        } elseif ($keys = call_user_func(array(get_class($obj), 'keys'))) {
//            $inst->_objects_keys[$class] = $keys;
//        } else {
//            $inst->_objects_keys[$class] = null;
//        }
//        if ($keys) {
//            foreach ($keys as $name => $field) {
//                if (is_array($field)) {
//                    $_key = "{$class}.{$name}.".join('.', array_map(
//                            function($f) use ($obj) {
//                                return $obj->$f;
//                            }, $field)
//                    );
//                } else {
//                    $_key = "{$class}.{$name}.{$obj->$field}";
//                }
//                $inst->all[$_key] = $obj;
//            }
//        }
    }

    /**
     * @param string $class
     * @param array  $index
     *
     * @return null|Object
     */
    static public function existsByIndex($class, array $index)
    {
        $inst = self::instance();
        list($name, $criteria) = each($index);
        $_key = "{$class}.{$name}.".join('.', array_map(
            function($f) use ($criteria) {
                return $criteria[$f];
            }, $criteria));
        if (isset($inst->all[$_key])) {
            return $inst->all[$_key];
        }
        return null;
    }

    /**
     * Удаление объекта из хранилища
     * @static
     *
     * @param Object $obj
     */
    static public function del(Object $obj)
    {
        self::addClean($obj);

        $inst = self::instance();
        $key  = $inst->globalKey($obj);
        if (isset($inst->all[$key])) {
            unset($inst->all[$key]);
        }
    }

    static public function addDirty(Object $obj)
    {
        $inst = self::instance();
        if (!in_array(null, $obj->pkValues(), true) && !in_array($obj, $inst->new, true)) {
            $inst->dirty[$inst->globalKey($obj)] = $obj;
        }
//        debugVar( $inst->globalKey( $obj ), 'addDirty' );
    }

    static public function addNew(Object $obj)
    {
        $inst = self::instance();
        if (isset($inst->dirty[$inst->globalKey($obj)])) {
            unset($inst->dirty[$inst->globalKey($obj)]);
        }
        foreach ($inst->new as $newObj) {
            if ($newObj === $obj) {
                return;
            }
        }
        $inst->new[] = $obj;
    }

    static public function addClean(Object $obj)
    {
        $inst = self::instance();
        unset($inst->dirty[$inst->globalKey($obj)]);
        unset($inst->delete[$inst->globalKey($obj)]);
        if (in_array($obj, $inst->new, true)) {
            $inst->new = array_filter(
                $inst->new,
                function ($newObj) use ($obj) {
                    return $newObj !== $obj;
                }
            );
        }
        //        debugVar( $inst->globalKey( $obj ), 'addClean' );
    }

    static public function addDelete(Object $obj)
    {
        $inst = self::instance();
        $key  = $inst->globalKey($obj);
        unset($inst->all[$key]);
        self::addClean($obj);
        $inst->delete[$key] = $obj;
    }

    /**
     * Выполнение операций
     * @return bool
     */
    public function performOperations()
    {
        if (!($this->dirty || $this->new || $this->delete)) {
            return true;
        }

        Model::getDB()->beginTransaction();

        try {
            if (is_array($this->dirty)) {
                /** @var Object $obj */
                foreach ($this->dirty as $obj) {
                    $obj->save();
                }
            }
            if (is_array($this->new)) {
                /** @var Object $obj */
                foreach ($this->new as $obj) {
                    $obj->save(true);
                }
            }
            if (is_array($this->delete)) {
                /** @var Object $obj */
                foreach ($this->delete as $obj) {
                    $obj->delete();
                }
            }
            $this->dirty  = array();
            $this->new    = array();
            $this->delete = array();
            Model::getDB()->commit();
        } catch (\PDOException $e) {
            Model::getDB()->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * Проверить существование объекта
     * @static
     *
     * @param  $classname
     * @param  $id
     *
     * @return Object
     */
    static public function exists($classname, $id)
    {
        $inst = self::instance();
        $key  = $inst->createGlobalKey($classname, $id);
        if (isset($inst->all[$key])) {
            return $inst->all[$key];
        }

        return null;
    }
}
