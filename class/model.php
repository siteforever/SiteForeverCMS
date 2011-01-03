<?php

class ModelException extends Exception {};

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

    /**
     * @var Data_Table
     */
    protected $table = null;

    protected $data = array();

    protected $fields   = null;

    protected static $all_class = array();
    protected static $exists_tables;

    /**
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Создание модели
     */
    function __construct()
    {
        // освобождаем потомков от зависимости от приложения
        if ( ! is_null( App::$db ) ) {
            $this->db       =& App::$db;
            if ( is_null( $this->pdo ) ) {
                $this->pdo        = App::$db->getResource();
            }
        }

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

        if ( ! is_null( $this->table ) ) {
            $this->fields   = $this->db->getFields( $this->table );
        }
    }

    /**
     * Вернет значение поля id
     * @return array|null
     */
    function getId()
    {
        if ( isset($this->data['id']) ) {
            return $this->data['id'];
        }
        return null;
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

    /**
     * Пакетный возврат данных (например в форму или представление)
     * @return array
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * Пакетная загрузка данных в модель (например из формы)
     * @param array $data
     * @return Model
     */
    function setData( $data )
    {
        if ( ! is_null( $this->fields ) ) {
            $this->data = array();
            foreach( $this->fields as $field ) {
                if ( isset( $data[$field] ) ) {
                    $this->data[$field] = $data[$field];
                }
            }
        }
        else {
            $this->data = $data;
        }
        return $this;
    }

    /**
     * Установка значения поля
     * @param  $key
     * @param  $value
     * @return Model
     */
    function set( $key, $value )
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Вернет значение поля
     * @param  $key
     * @return array|null
     */
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
     * @param int|array $id
     * @return array
     */
    function find( $id )
    {
        if ( is_numeric( $id ) ) {
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
        $this->setData( $data );
        return $data;
    }

    /**
     * Поиск по критерию
     * @param array $crit
     * @return void
     */
    function findAll( $crit = array() )
    {
        $criteria   = new Data_Criteria( $this->table, $crit );
        $raw    = $this->db->fetchAll($criteria->getSQL(), false, DB::F_ASSOC, $criteria->getParams() );
        if ( $raw ) {
            return $raw;
        }
        return array();
    }

    /**
     * Сохраняет данные модели в базе
     * @return int
     */
    function save()
    {
        if ( $this->getId() ) {
            return $this->db->update( $this->table, $this->data, 'id = '.$this->getId() );
        }
        else {
            $ins = $this->db->insert( $this->table, $this->data );
            if ( $ins ) {
                $this->set('id', $ins);
            }
            return $ins;
        }
    }

    /**
     * Удаляет строку из таблицы
     * @param null $id
     * @return bool|mixed
     */
    function delete( $id = null )
    {
        if ( is_null($id) ) {
            if ( $this->getId() ) {
                $id = $this->getId();
            } else {
                return false;
            }
        }
        return $this->db->delete($this->table, 'id = :id', array(':id'=>$id));
    }

}