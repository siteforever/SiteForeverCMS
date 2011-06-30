<?php

class ModelException extends Exception {};

/**
 * Интерфейс модели
 */
abstract class Model
{
    const HAS_MANY  = 'has_many';   // содержет много
    const HAS_ONE   = 'has_one';    // содержет один
    const BELONGS   = 'belongs';    // принадлежит
    const MANY_MANY = 'many_many';  // много ко многим

    const STAT      = 'stat';       // статистическая связь

    /**
     * @var db
     */
    protected $db;
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Data_Object_User
     */
    protected $user;

    /**
     * @var Data_Table
     */
    protected $table = null;

    /**
     * @var Application_Abstract
     */
    protected $app;

    /**
     * @var form_Form
     */
    //protected $form;

    /**
     * @var Data_Object
     */
    protected $data;

    protected static $fields   = array();

    protected static $all_class = array();
    protected static $exists_tables;
    protected static $dao;

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
     * Создание модели
     */
    final private function __construct()
    {
        $this->request  = $this->app()->getRequest();
        $this->config   = $this->app()->getConfig();
        //$this->user     = $this->app()->getAuth()->currentUser();


        // база данных
        // определяется только в моделях
        if ( is_null( self::$dao ) ) {
            self::$dao  = db::getInstance( $this->config->get('db') );
            self::$dao->setLoggerClass( $this->app()->getLogger() );
        }
        $this->db   = self::$dao;
        $this->pdo  = $this->db->getResource();
        

        if ( ! isset( self::$exists_tables ) ) {
            self::$exists_tables    = array();
            $tables = $this->db->fetchAll("SHOW TABLES", false, DB::F_ARRAY);
            foreach ( $tables as $t ) {
                self::$exists_tables[]  = $t[0];
            }
        }

        $this->init();
    }

    /**
     * @static
     * @return db
     */
    static public function getDB()
    {
        return self::$dao;
    }

    /**
     * @return Application_Abstract
     */
    final function app()
    {
        if ( is_null( $this->app ) ) {
            $this->app  = App::getInstance();
        }
        return $this->app;
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
     * @return array
     */
    function relation()
    {
        return array();
    }

    /**
     * Проверяет существование таблицы
     * @param Data_Table $table
     * @return bool
     */
    private function isExistTable( Data_Table $table )
    {
        return in_array( (string) $table, self::$exists_tables );
    }

    /**
     * Добавит созданную таблицу в кэш
     * @param Data_Table $table
     * @return void
     */
    private function addNewTable( Data_Table $table )
    {
        $this->getDB()->query( $this->table->getCreateTable() );
        self::$exists_tables[]  = (string) $table;
    }

    /**
     * Вернет нужную модель
     * @static
     * @param  $class_name
     * @return Model
     */
    final static public function getModel( $class_name )
    {
        if ( strpos( strtolower( $class_name ), 'model_' ) === false ) {
            $class_name = 'Model_'.$class_name;
        }
        //var_dump( isset( self::$all_class[ $class_name ] ) );

        if ( ! isset( self::$all_class[ $class_name ] ) )
        {
            if ( class_exists($class_name, true) ) {
                self::$all_class[ $class_name ] = new $class_name();
            }
            else {
                throw new Exception('Model "'.$class_name.'" not found');
            }
        }
        return self::$all_class[ $class_name ];
    }

    /**
     * Создать объект
     * @param array $data
     * @return Data_Object
     */
    final public function createObject( $data = array() )
    {
        if ( isset( $data['id'] ) ) {
            $obj    = $this->getFromMap( $data['id'] );
            if ( $obj ) {
                $obj->setAttributes( $data );
                return $obj;
            }
        }
        $class_name = $this->objectClass();
        $obj    = new $class_name( $this, $data );
        if ( ! is_null( $obj->getId() ) ) {
            $this->addToMap( $obj );
            $obj->markClean();
        }
        return $obj;
    }


    /**
     * Адаптер к наблюдателю для получения объекта
     * @param int $id
     * @return Data_Object|null
     */
    private function getFromMap( $id )
    {
        return Data_Watcher::exists( $this->objectClass(), $id );
    }

    /**
     * Адаптер к наблюдателю для добавления объекта
     * @param Data_Object $obj
     */
    private function addToMap( Data_Object $obj )
    {
        return Data_Watcher::add( $obj );
    }


    /**
     * Класс для контейнера данных
     * @return string
     */
    //public abstract function objectClass();
    public function objectClass()
    {
        return 'Data_Object_'.substr( get_class( $this ), 6 );
    }

    /**
     * Класс таблицы БД
     * @abstract
     * @return string
     */
    //abstract public function tableClass();
    public function tableClass()
    {
        return 'Data_Table_'.substr( get_class( $this ), 6 );
    }

    /**
     * Вернет таблицу модели
     * @return Data_Table|null
     */
    final public function getTable()
    {
        if ( is_null( $this->table ) ) {
            $class_name = $this->tableClass();

            $this->table    = new $class_name();

            if ( ! $this->isExistTable( $this->table ) ) {
                $this->addNewTable( $this->table );
                $this->onCreateTable();
            }
        }

        return $this->table;
    }

    /**
     * Событие возникает при создании новой таблицы
     * @return void
     */
    protected function onCreateTable()
    {
    }

    /**
     * Вернет текстовое имя таблицы
     * @return string
     */
    final public function getTableName()
    {
        return (string) $this->getTable();
    }

    /**
     * Установить связи для следующего запроса
     * @return Model
     */
    function with()
    {
        $this->with = func_get_args();
        return $this;
    }

    /**
     * Finding data by primary key
     * @throws Exception
     * @param int|array|Data_Criteria $crit
     * @return Data_Object
     */
    final public function find( $crit )
    {
        $this->with = array();

        if ( is_object( $crit ) ) {
            if ( $crit instanceof Db_Criteria ) {
                $criteria   = new Data_Criteria($this->getTable(), $crit);
            }
            elseif ( $crit instanceof Data_Criteria ) {
                $criteria   = $crit;
            }
        }

        // не определился критерий, но параметр - число
        // тогда полагаем, что параметр - это ID объекта
        if ( ! isset ( $criteria ) && is_numeric( $crit ) ) {
            $obj = $this->getFromMap( $crit );
            if ( $obj ) {
                return $obj;
            }
            $crit   = array(
                'cond'  => 'id = :id',
                'params'=> array(':id'=>$crit),
                'limit' => '1',
            );
        } elseif ( is_array( $crit ) ) {
            $default = array(
                'select'    => '*',
                'cond'      => 'id = :id',
                'params'    => array(':id'=>1),
                'limit'     => '1',
            );
            $crit   = array_merge($default,$crit);
        } else {
            throw new ModelException('Not valid criteria');
        }

        if ( ! isset( $criteria ) && isset( $crit ) && is_array( $crit ) ) {
            $criteria   = new Data_Criteria( $this->getTable(), $crit );
        }

        $data = $this->db->fetch( $criteria->getSQL(), DB::F_ASSOC, $crit['params'] );

        if ( $data ) {
            $obj_data   = $this->createObject( $data );
            return $obj_data;
        }
        return null;
    }

    /**
     * Поиск по критерию
     * @param array $crit
     * @param bool $do_index
     * @return array
     */
    final public function findAll( $crit = array() )
    {
        $with   = $this->with;
        $this->with = array();

        if ( is_array( $crit ) || ( is_object( $crit ) && $crit instanceof Db_Criteria ) ) {
            $criteria   = new Data_Criteria( $this->getTable(), $crit );
        }
        elseif ( is_object( $crit ) && $crit instanceof Data_Criteria ) {
            $criteria   = $crit;
        }
        else {
            throw new ModelException('Not valid criteria');
        }

        $raw    = $this->db->fetchAll($criteria->getSQL());
        $collection = array();

        if ( $raw ) {
            foreach ( $raw as $key => $data ) {
                $collection[$key]   = $this->createObject( $data );
            }
        }

        if ( count( $raw ) && count( $with ) ) {
            $relation   = $this->relation();

            $keys   = array_keys( $collection );
            foreach ( $with as $rel ) {

                $model  = self::getModel( $relation[ $rel ][1] );
                $key    = $relation[ $rel ][2];

                switch ( $relation[$rel][0] ) {
                    case self::HAS_ONE:

                        $objects    = $model->findAll(array(
                            'cond'      => " $key IN ( ? ) ",
                            'params'    => array( implode(",", $keys) ),
                        ));

                        break;
                }
            }
        }

        return $collection;
    }

    /**
     * Поиск по отношению
     * @param string $rel
     * @param mixed $data
     * @return array|Data_Object
     */
    final public function findByRelation( $rel, $data )
    {
        if ( is_object( $data ) && $data instanceof Data_Object ) {
            $obj    = $data;
        }

        $relation   = $this->relation();

        $key        = $relation[ $rel ][2];

        $model  = self::getModel( $relation[ $rel ][1] );

        if (    $relation[ $rel ][0] == self::HAS_ONE ||
                $relation[ $rel ][0] == self::HAS_MANY
        ) {
            $criteria = array(
                'cond'  => " {$key} IN (:key) ",
                'params'=> array(":key"=>$obj->getId()),
            );
        }

        switch ( $relation[$rel][0] ) {
            case self::HAS_ONE:
                return $model->find( $criteria );
                break;

            case self::HAS_MANY:
                return $model->findAll( $criteria );
                break;

            case self::BELONGS:
                if ( $obj->$key ) {
                    return $model->find( $obj->$key );
                }
                return null;
                break;

            case self::MANY_MANY:

                break;

            case self::STAT:
                $model->count( $criteria['cond'], $criteria['params'] );
                break;
        }
    }

    /**
     * Сохраняет данные модели в базе
     * @param Data_Object $obj
     * @return int
     */
    public function save( Data_Object $obj )
    {
        $data   = $obj->getAttributes();

        $fields = $this->table->getFields();

        $save_data  = array();

        /**
         * @var Data_Field $field
         */
        foreach ( $fields as $field ) {
            if ( isset( $data[ $field->getName() ] ) ) {
                $save_data[ $field->getName() ] = $data[ $field->getName() ];
            }
        }

        $ret    = null;
        if ( $obj->getId() ) {
            $ret = $this->db->update( $this->getTableName(), $save_data, 'id = '.$obj->getId() );
            $obj->markClean();
        }
        else {
            $ret = $this->db->insert( $this->getTableName(), $save_data );
            $obj->id  = $ret;
            $this->addToMap( $obj );
        }
        return $ret;
    }

    /**
     * Удаляет строку из таблицы
     * @param int $id
     * @return bool|mixed
     */
    final public function delete( $id )
    {
        if ( $this->onDeleteStart( $id ) === false ) {
            return false;
        }

        $obj    = $this->find( $id );
        if ( $obj ) {
            Data_Watcher::del( $obj );
            if ( $this->getDB()->delete($this->getTableName(), 'id = :id', array(':id'=>$obj->getId())) ) {
                $this->onDeleteSuccess( $id );
                return true;
            }
        }
    }

    /**
     * Событие, вызывается перед удалением объекта
     * Если вернет false, объект не будет удален
     * @return bool
     */
    public function onDeleteStart( $id = null ) {
        return true;
    }

    /**
     * Событие, вызывается после успешного удаления объекта
     * @param int $id
     * @return bool
     */
    public function onDeleteSuccess( $id = null ) {
        return true;
    }

    /**
     * Вернет количество записей по условию
     * @param string $cond
     * @param array $params
     * @return int
     */
    final public function count( $cond = '', $params = array() )
    {
        $criteria   = new Data_Criteria($this->getTable(), array(
            'select'    => 'COUNT(*)',
            'cond'      => $cond,
            'params'    => $params,
        ));

        $sql    = $criteria->getSQL();

        $count  = $this->db->fetchOne( $sql );
        
        if ( $count )
            return $count;

        return 0;
    }

}