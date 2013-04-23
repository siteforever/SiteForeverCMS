<?php
/**
 * Наблюдатель за объектами данных
 * @author keltanas
 */
namespace Sfcms\Data;

use Sfcms\Kernel\KernelBase;
use Sfcms\Model;
use Sfcms\Data\Object;

class Watcher
{
    private $all    = array();
    private $dirty  = array();
    private $new    = array();
    private $delete = array();

    /**
     * @var Watcher
     */
    private static $instance;

    private function __construct() {}

    /**
     * @static
     * @return Watcher
     */
    static function instance()
    {
        if ( ! isset(self::$instance) )
        {
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
     * @return string|null
     */
    public function globalKey( Object $obj )
    {
        if ( $obj->id ) {
            return $this->createGlobalKey( get_class( $obj ), $obj->id );
        }
        return null;
    }

    public function dumpAll()
    {
        $ret    = array();
        foreach ( $this->all as $key => $obj ) {
            $ret[ $key ] = (string) $obj . ' ' . print_r($obj->attributes,1);
        }
        return $ret;
    }

    public function dumpDirty()
    {
        $ret    = array();
        foreach ( $this->dirty as $key => $obj ) {
            $ret[ $key ]    = (string) $obj;
        }
        return $ret;
    }

    public function dumpNew()
    {
        $ret    = array();
        foreach ( $this->new as $key => $obj ) {
            $ret[ $key ]    = (string) $obj;
        }
        return $ret;
    }

    /**
     * Создает ключ для объекта
     * @param  $class_name
     * @param  $id
     * @return string
     */
    private function createGlobalKey( $class_name, $id )
    {
        return $class_name.'.'.$id;
    }

    /**
     * Добавить объект
     * @static
     * @param Object $obj
     */
    static public function add( Object $obj )
    {
        $inst = self::instance();
        $inst->all[ $inst->globalKey($obj) ] = $obj;
    }

    /**
     * Удаление объекта из хранилища
     * @static
     * @param Object $obj
     */
    static public function del( Object $obj )
    {
        self::addClean( $obj );

        $inst   = self::instance();
        $key    = $inst->globalKey($obj);
        if ( isset( $inst->all[ $key ] ) ) {
            unset( $inst->all[ $key ] );
        }
    }

    static public function addDirty( Object $obj )
    {
        $inst = self::instance();
        if ( $obj->getId() && ! in_array( $obj, $inst->new, true ) ) {
            $inst->dirty[ $inst->globalKey( $obj ) ] = $obj;
        }
//        debugVar( $inst->globalKey( $obj ), 'addDirty' );
    }

    static public function addNew( Object $obj )
    {
        $inst = self::instance();
        if ( isset( $inst->dirty[$inst->globalKey($obj)] ) ) {
            unset( $inst->dirty[$inst->globalKey($obj)] );
        }
        $have = false;
        foreach( $inst->new as $newObj ) {
            if ( $newObj === $obj ) {
                $have = true;
                break;
            }
        }
        if ( ! $have )
            $inst->new[] = $obj;
    }

    static public function addClean( Object $obj )
    {
        $inst = self::instance();
        unset( $inst->dirty[$inst->globalKey($obj)] );
        if ( /*! $obj->getId() &&*/ in_array( $obj, $inst->new, true ) )
        {
            $pruned = array();
            foreach( $inst->new as $newobj ) {
                if ( ! ( $newobj === $obj ) ) {
                    $pruned[] = $newobj;
                }
            }
            $inst->new = $pruned;
        }
//        debugVar( $inst->globalKey( $obj ), 'addClean' );
    }

    static public function addDelete( Object $obj )
    {
        $inst = self::instance();
        $key    = $inst->globalKey($obj);
        unset( $inst->all[ $key ] );
        self::addClean( $obj );
        $inst->delete[ $key ] = $obj;
    }

    /**
     * Выполнение операций
     * @return bool
     */
    public function performOperations()
    {
        $pdo    = Model::getDB()->getResource();
        $pdo->beginTransaction();

        try {
            if ( is_array( $this->dirty ) ) {
                /** @var Object $obj */
                array_walk($this->dirty, function($obj){
                    $obj->save();
                });
            }
            if ( is_array( $this->new ) ) {
                /** @var Object $obj */
                array_walk($this->new, function($obj){
                    $obj->save();
                });
            }
            if ( is_array( $this->delete ) ) {
                /** @var Object $obj */
                array_walk($this->delete, function($obj){
                    $obj->delete();
                });
            }
            $this->dirty = array();
            $this->new   = array();
            $this->delete= array();
        } catch ( \PDOException $e ) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        return true;
    }

    /**
     * Проверить существование объекта
     * @static
     * @param  $classname
     * @param  $id
     * @return Object
     */
    static public function exists( $classname, $id )
    {
        $inst   = self::instance();
        $key    = $inst->createGlobalKey( $classname, $id );
        if ( isset( $inst->all[ $key ] ) )
        {
            return $inst->all[$key];
        }
        return null;
    }
}