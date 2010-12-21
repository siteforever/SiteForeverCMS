<?php
/**
 * Интерфейс модели
 */
abstract class Model
{
    // @TODO Нужен способ обмена данными между контроллером и моделью
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

    protected $table;

    protected $data = array();

    protected static $all_class = array();
    protected static $exists_tables;

    function __construct( $fail = true )
    {
        if ( $fail ) {
            throw new Exception('This is private method. Need use Model::getModel()');
        }
        // освобождаем потомков от зависимости от приложения
        $this->db       =& App::$db;
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
        $this->createTables();
    }

    function getId()
    {
        return $this->data['id'];
    }

    /**
     * Создание таблиц
     * @return void
     */
    function createTables()
    {
    }

    /**
     * Проверяет существование таблицы
     * @param string $table
     * @return bool
     */
    function isExistTable( $table )
    {
        return in_array( $table, self::$exists_tables );
    }

    /**
     * Вернет нужную модель
     * @static
     * @param  $class_name
     * @return Model
     */
    static function getModel( $class_name )
    {
        if ( ! isset( self::$all_class[ $class_name ] ) )
        {
            if ( class_exists($class_name) ) {
                self::$all_class[ $class_name ] = new $class_name(false);
            }
            else {
                throw new Exception('Model "'.$class_name.'" not found');
            }
        }
        return self::$all_class[ $class_name ];
    }

    function getData()
    {
        return $this->data;
    }

    function setData( $data )
    {
        $this->data = $data;
        return $this;
    }

    function set( $key, $value )
    {
        $this->data[$key] = $value;
        return $this;
    }

    function get( $key )
    {
        if ( isset($this->data[$key]) ) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * Finding data by primary key
     * @throws Exception
     * @param  $id
     * @return array
     */
    function find( $id )
    {
        if ( isset( $this->table ) && $this->isExistTable( $this->table ) ) {
            return $this->db->fetch(
                "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1",
                 db::F_ASSOC,
                 array(':id'=>$id) );
        }
        throw new Exception('Table for model not found');
    }


}