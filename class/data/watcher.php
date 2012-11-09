<?php
/**
 * Наблюдатель за объектами данных
 * @author keltanas
 */
class Data_Watcher
{
    private $all    = array();
    private $dirty  = array();
    private $new    = array();
    private $delete = array();

    /**
     * @var Data_Watcher
     */
    private static $instance;

    private function __construct() {}

    /**
     * @static
     * @return Data_Watcher
     */
    static function instance()
    {
        if ( ! isset(self::$instance) )
        {
            self::$instance = new Data_Watcher();
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
     * @param Data_Object $obj
     * @return string
     */
    public function globalKey( Data_Object $obj )
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
     * @param Data_Object $obj
     * @return void
     */
    static public function add( Data_Object $obj )
    {
        $inst = self::instance();
        $inst->all[ $inst->globalKey($obj) ] = $obj;
    }

    /**
     * Удаление объекта из хранилища
     * @static
     * @param Data_Object $obj
     * @return void
     */
    static public function del( Data_Object $obj )
    {
        self::addClean( $obj );

        $inst   = self::instance();
        $key    = $inst->globalKey($obj);
        if ( isset( $inst->all[ $key ] ) ) {
            unset( $inst->all[ $key ] );
        }
    }

    static public function addDirty( Data_Object $obj )
    {
        $inst = self::instance();
        if ( $obj->getId() && ! in_array( $obj, $inst->new, true ) ) {
            $inst->dirty[ $inst->globalKey( $obj ) ] = $obj;
        }
    }

    static public function addNew( Data_Object $obj )
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

    static public function addClean( Data_Object $obj )
    {
        $inst = self::instance();
        unset( $inst->dirty[$inst->globalKey($obj)] );
        if ( in_array( $obj, $inst->new, true ) )
        {
//            $inst->new = array_filter($inst->new, function( $o )use( $obj ){
//                return $o !== $obj;
//            });
            $pruned = array();
            foreach( $inst->new as $newobj ) {
                if ( ! ( $newobj === $obj ) ) {
                    $pruned[] = $newobj;
                }
            }
            $inst->new = $pruned;
        }
    }

    static public function addDelete( Data_Object $obj )
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
        /** @var Data_Object $obj */
        $pdo    = Sfcms_Model::getDB()->getResource();
        $pdo->beginTransaction();

        try {
            if ( is_array( $this->dirty ) ) {
                array_walk($this->dirty, function($obj){
                    $obj->save();
                });
            }
            if ( is_array( $this->new ) ) {
                array_walk($this->new, function($obj){
                    $obj->save();
                });
            }
            if ( is_array( $this->delete ) ) {
                array_walk($this->delete, function($obj){
                    $obj->delete();
                });
            }
            $this->dirty = array();
            $this->new   = array();
            $this->delete= array();
        } catch ( PDOException $e ) {
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
     * @return Data_Object
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