<?php

class ModelException extends Exception {};

/**
 * Интерфейс модели
 */
abstract class Model
{
    // @TODO Нужен способ обмена данными между контроллером и моделью
    // @TODO Тестирование классов Data_Criteria и Data_Object
    /**
     * @var db
     */
    protected $db;
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var model_User
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

    /**
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Создание модели
     */
    private function __construct()
    {
        // освобождаем потомков от зависимости от приложения
        if ( ! is_null( App::$db ) ) {
            $this->db       =& App::$db;
            if ( is_null( $this->pdo ) ) {
                $this->pdo        = App::$db->getResource();
            }
        }

        $this->app      = App::getInstance();

        $this->request  =& App::$request;
        $this->user     =& App::$user;
        $this->config   =& App::$config;

        if ( ! isset( self::$exists_tables ) ) {
            self::$exists_tables    = array();
            $tables = $this->db->fetchAll("SHOW TABLES", false, db::F_ARRAY);
            foreach ( $tables as $t ) {
                self::$exists_tables[]  = $t[0];
            }
        }

        if ( $this->getTable() && ! isset( self::$fields[(string)$this->table] ) ) {
            self::$fields[(string) $this->table]   = $this->db->getFields( (string) $this->table );
        }

        $this->Init();
    }

    /**
     * Инициализация
     * @return void
     */
    protected function Init()
    {
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
        $this->db->query($this->table->getCreateTable());
        self::$exists_tables[]  = (string) $table;
    }

    /**
     * Вернет нужную модель
     * @static
     * @param  $class_name
     * @return Model
     */
    static public function getModel( $class_name )
    {
        if ( strpos( strtolower( $class_name ), 'model_' ) === false ) {
            $class_name = 'model_'.$class_name;
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
    public function createObject( $data = array() )
    {
        if ( isset( $data['id'] ) ) {
            $obj    = $this->getFromMap( $data['id'] );
            if ( $obj ) {
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
    public abstract function objectClass();

    /**
     * @abstract
     * @return string
     */
    abstract public function tableClass();

    /**
     * Установка значения поля
     * @deprecated
     * @param  $key
     * @param  $value
     * @return Model
     */
    public function set( $key, $value )
    {
        App::getInstance()->getLogger()->log('use deprecated method '.__METHOD__);
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Вернет значение поля
     * @deprecated
     * @param  $key
     * @return array|null
     */
    public function get( $key )
    {
        App::getInstance()->getLogger()->log('use deprecated method '.__METHOD__);
        if ( isset($this->data[$key]) ) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * Вернет таблицу модели
     * @return Data_Table|null
     */
    public function getTable()
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
    public function getTableName()
    {
        return (string) $this->getTable();
    }

    /**
     * Finding data by primary key
     * @throws Exception
     * @param int|array $id
     * @return Data_Object
     */
    public function find( $id )
    {
        if ( empty( $id ) ) {
            $id = 0;
        }

        if ( is_numeric( $id ) ) {
            $obj = $this->getFromMap( $id );
            if ( $obj ) {
                return $obj;
            }
            $criteria   = new Data_Criteria($this->table, array(
                                   'cond'  => 'id = :id',
                                   'params'=> array(':id'=>$id),
                                   'limit' => '1',
                              ));
        } elseif ( is_array( $id ) ) {

            $default = array(
                'select'    => '*',
                'cond'      => 'id = :id',
                'params'    => array(':id'=>1),
                'limit'     => '1',
            );

            $criteria   = new Data_Criteria($this->table, array_merge($default,$id));
        } else {
            throw new ModelException('Undefined parameter format');
        }

        $data = $this->db->fetch(
            $criteria->getSQL(),
            db::F_ASSOC,
            $criteria->getParams()
        );

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
    public function findAll( $crit = array(), $do_index   = false )
    {
        $criteria   = new Data_Criteria( $this->table, $crit );

        //printVar($criteria->getSQL());

        $raw    = $this->db->fetchAll($criteria->getSQL(), $do_index, DB::F_ASSOC, $criteria->getParams() );
        $collection = array();
        //printVar($raw);
        if ( $raw ) {
            foreach ( $raw as $key => $data ) {
                $collection[$key]   = $this->createObject( $data );
            }
        }
        return $collection;
    }

    /**
     * Сохраняет данные модели в базе
     * @param Data_Object $obj
     * @return int
     */
    public function save( Data_Object $obj )
    {
        $data   = $obj->getAttributes();
        $ret    = false;

        if ( isset( $data['id'] ) && is_numeric( $data['id'] ) && $data['id'] ) {
            $ret = $this->db->update( (string) $this->table, $data, 'id = '.$data['id'] );
            $obj->markClean();
        }
        else {
            $ret = $this->db->insert( (string) $this->table, $data );
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
    public function delete( $id )
    {
        if ( $id ) {
            $this->db->delete($this->table, 'id = :id', array(':id'=>$id));
        }
    }

    /**
     * Вернет количество записей по условию
     * @param string $cond
     * @param array $params
     * @return int
     */
    public function count( $cond = '', $params = array() )
    {
        $sql    = array();
        $sql[]  = "SELECT COUNT(*) FROM {$this->table}";
        if ( $cond )
            $sql[] = "WHERE {$cond}";

        $count  = $this->db->fetchOne(
            join("\n", $sql), $params
        );
        if ( $count )
            return $count;

        return 0;
    }

}