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
     * Определить ключ для объекта
     * @param Data_Object $obj
     * @return string
     */
    function globalKey( Data_Object $obj )
    {
        if ( $obj->id ) {
            return $this->createGlobalKey( get_class( $obj ), $obj->id );
        }
        return null;
    }

    function dumpAll()
    {
        $ret    = array();
        foreach ( $this->all as $key => $obj ) {
            $ret[ $key ]    = (string) $obj;
        }
        return $ret;
    }

    function dumpDirty()
    {
        $ret    = array();
        foreach ( $this->dirty as $key => $obj ) {
            $ret[ $key ]    = (string) $obj;
        }
        return $ret;
    }

    function dumpNew()
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
    static function add( Data_Object $obj )
    {
        $inst = self::instance();
        $inst->all[ $inst->globalKey($obj) ] = $obj;
    }

    static function addDirty( Data_Object $obj )
    {
        $inst = self::instance();
        if ( $obj->id && ! in_array( $obj, $inst->new, true ) ) {
            $inst->dirty[ $inst->globalKey( $obj ) ] = $obj;
        }
    }

    static function addNew( Data_Object $obj )
    {
        $inst = self::instance();
        if ( isset( $inst->dirty[$inst->globalKey($obj)] ) ) {
            unset( $inst->dirty[$inst->globalKey($obj)] );
        }
        $inst->new[] = $obj;
    }

    static function addClean( Data_Object $obj )
    {
        $inst = self::instance();
        unset( $inst->dirty[$inst->globalKey($obj)] );

        /*if ( $key = array_search( $obj, $inst->new, true ) ) {
            unset( $inst->new[ $key ] );
        }*/
        if ( in_array( $obj, $inst->new, true ) )
        {
            $pruned = array();
            foreach( $inst->new as $newobj ) {
                if ( ! ( $newobj === $obj ) ) {
                    $pruned[] = $newobj;
                }
            }
            $inst->new = $pruned;
        }
    }

    static function addDelete( Data_Object $obj )
    {
        $inst = self::instance();
        $key    = $inst->globalKey($obj);
        unset( $inst->all[ $key ] );
        self::addClean( $obj );
        $inst->delete[ $key ] = $obj;
    }

    /**
     * Выполнение операций
     * @return void
     */
    function performOperations()
    {
        /**
         * @var Data_Object $obj
         */
        $pdo    = App::$db->getResource();
        $pdo->beginTransaction();
        try {
            if ( is_array( $this->dirty ) ) {
                foreach( $this->dirty as $key => $obj ) {
                    $obj->getModel()->save( $obj );
                }
            }
            if ( is_array( $this->new ) ) {
                foreach( $this->new as $key => $obj ) {
                    $obj->getModel()->save( $obj );
                }
            }
            if ( is_array( $this->delete ) ) {
                foreach( $this->delete as $key => $obj ) {
                    $obj->getModel()->delete( $obj->getId() );
                }
            }
            $this->dirty = array();
            $this->new   = array();
            $this->delete= array();
        } catch ( PDOException $e ) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
    }

    /**
     * Проверить существование объекта
     * @static
     * @param  $classname
     * @param  $id
     * @return Data_Object
     */
    static function exists( $classname, $id )
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