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

        if ( $this->getTable() && ! isset( self::$fields[(string)$this->table] ) ) {
            self::$fields[(string) $this->table]   = $this->db->getFields( (string) $this->table );
        }

        $this->Init();
    }

    /**
     * Вернет значение поля id
     * @return array|null
     */
    public function getId()
    {
        App::getInstance()->getLogger()->log('use deprecated method '.__CLASS__.'::'.__METHOD__);
        if ( isset($this->data['id']) ) {
            return $this->data['id'];
        }
        return null;
    }

    /**
     * Инициализация
     * @return void
     */
    protected function Init()
    {
    }

    /**
     * Создание таблиц
     * @return void
     */
    public function createTables()
    {
        return false;
    }

    /**
     * Проверяет существование таблицы
     * @param string $table
     * @return bool
     */
    public function isExistTable( $table )
    {
        return in_array( (string) $table, self::$exists_tables );
    }

    /**
     * Вернет нужную модель
     * @static
     * @param  $class_name
     * @return Model
     */
    static public function getModel( $class_name )
    {
        if ( ! isset( self::$all_class[ $class_name ] ) )
        {
            if ( class_exists($class_name) ) {
                self::$all_class[ $class_name ] = new $class_name();
            }
            else {
                throw new Exception('Model "'.$class_name.'" not found');
            }
        }
        return self::$all_class[ $class_name ];
    }

    /**
     * Пакетный возврат данных (например в форму или представление)
     * @deprecated
     * @return Data_Object
     */
    public function getData()
    {
        App::getInstance()->getLogger()->log('use deprecated method '.__CLASS__.'::'.__METHOD__);
        if ( is_array( $this->data ) ) {
            $this->data = $this->createObject( $this->data );
        }

        return $this->data;
    }

    /**
     * Пакетная загрузка данных в модель (например из формы)
     * @deprecated
     * @param array|Data_Object $data
     * @return Model
     */
    public function setData( $data )
    {
        App::getInstance()->getLogger()->log('use deprecated method '.__CLASS__.'::'.__METHOD__);
        if ( is_array( $data ) )
            $this->data = $this->createObject( $data );
        elseif ( $data instanceof Data_Object )
            $this->data = $data;
        return $this;
    }

    /**
     * Создать объект
     * @param array $array
     * @return Data_Object
     */
    public function createObject( $array = array() )
    {
        return new Data_Object( $this, $array );
    }

    /**
     * Установка значения поля
     * @deprecated
     * @param  $key
     * @param  $value
     * @return Model
     */
    public function set( $key, $value )
    {
        App::getInstance()->getLogger()->log('use deprecated method '.__CLASS__.'::'.__METHOD__);
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
        App::getInstance()->getLogger()->log('use deprecated method '.__CLASS__.'::'.__METHOD__);
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
        return $this->table;
    }

    /**
     * Установит активную таблицу
     * @param Data_Table $table
     * @return void
     */
    public function setTable( Data_Table $table )
    {
        $this->table    = $table;
    }

    /**
     * Finding data by primary key
     * @throws Exception
     * @param int|array $id
     * @return Data_Object
     */
    public function find( $id )
    {
        if ( is_numeric( $id ) ) {
            if ( $this->getId() == $id ) {
                return $this->data;
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
            $this->setData( $obj_data );
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
        $raw    = $this->db->fetchAll($criteria->getSQL(), $do_index, DB::F_ASSOC, $criteria->getParams() );
        $collection = array();
        if ( $raw ) {
            foreach ( $raw as $d ) {
                $collection[]   = $this->createObject( $d );
            }
        }
        return $collection;
    }

    /**
     * Сохраняет данные модели в базе
     * @param Data_Object $obj
     * @return int
     */
    public function save( Data_Object $obj = null )
    {
        if ( ! is_null( $obj ) ) {
            $data   = $obj->getAttributes();
        } else {
            $data   = $this->data;
        }

        if ( $data['id'] ) {
            return $this->db->update( (string) $this->table, $data, 'id = '.$this->getId() );
        }
        else {
            $ins = $this->db->insert( (string) $this->table, $data );
            $obj['id']  = $ins;
            return $ins;
        }
    }

    /**
     * Удаляет строку из таблицы
     * @param null $id
     * @return bool|mixed
     */
    public function delete( $id = null )
    {
        if ( is_null($id) ) {
            if ( $this->getId() ) {
                $id = $this->getId();
            } else {
                return false;
            }
        } elseif ( $id instanceof Data_Object ) {
            $id = $id['id'];
        }
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