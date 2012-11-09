<?php
/**
 * Интерфейс модели
 */
use Sfcms\Component;

abstract class Sfcms_Model extends Component
{
    const HAS_MANY      = 'has_many'; // содержет много
    const ONE_TO_MANY   = 'has_many'; // содержет много
    const HAS_ONE       = 'has_one'; // содержет один
    const ONE_TO_ONE    = 'has_one'; // содержет один
    const BELONGS       = 'belongs'; // принадлежит
    const MANY_TO_ONE   = 'belongs'; // принадлежит

    const STAT          = 'stat'; // статистическая связь

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
     * @var Form_Form
     */
    //protected $form;

    /**
     * @var Data_Object
     */
    protected $data;

    protected static $fields = array();

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

    private $_plugins = array();

    /**
     * Создание модели
     */
    final private function __construct()
    {
        $this->request = $this->app()->getRequest();
        $this->config  = $this->app()->getConfig();
        //$this->user     = $this->app()->getAuth()->currentUser();

        // база данных
        // определяется только в моделях
        if( is_null( self::$dao ) ) {
            self::$dao = db::getInstance( $this->config->get( 'db' ) );
            self::$dao->setLoggerClass( $this->app()->getLogger() );
        }
        $this->db  = self::$dao;
        $this->pdo = $this->db->getResource();

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
     * Инициализация
     * @return void
     */
    protected function init()
    {
    }


    /**
     * Вызывает нужные плугины
     * @param string $name
     * @param Data_Object $obj
     */
    protected function callPlugins( $name, Data_Object $obj )
    {
        if ( strpos( trim( $name, ':' ), ':' ) ) {
            list( $namespace, $name ) = explode( ':', $name );
        } else {
            $namespace = 'default';
        }
//        $this->log( 'ns: '.$namespace . '; pln: ' . $name, 'callModelPlugins' );
        // Если нет плагинов, ничего не делаем
        if ( ! isset( $this->_plugins[ $namespace ] ) ) {
            return;
        }
        foreach( $this->_plugins[$namespace] as $plugin ) {
//            $this->log( get_class($plugin), 'modelPlugin' );
            if ( method_exists( $plugin, $name ) ) {
                $plugin->$name( $obj );
            }
        }
    }

    /**
     * Добавляет плугин
     * @param \Sfcms\Model\Plugin $plugin
     * @param $namespace
     */
    protected function addPlugin( \Sfcms\Model\Plugin $plugin, $namespace = 'default' )
    {
        $this->_plugins[$namespace][] = $plugin;
    }


    /**
     * Отношения модели с другими моделями
     *
     * <p>Пример:</p>
     * <pre>array(
     *     'Category' => array(self::BELONGS, 'GalleryCategory', 'category_id'),
     * );</pre>
     *
     * @return array
     */
    public function relation()
    {
        return array();
    }

    /**
     * Проверяет существование таблицы
     * @param Data_Table $table
     * @return boolean
     */
    private function isExistTable( Data_Table $table )
    {
        if( ! isset( self::$exists_tables ) ) {
            self::$exists_tables = array();
            $tables = $this->db->fetchAll( "SHOW TABLES", false, DB::F_ARRAY );
            foreach( $tables as $t ) {
                self::$exists_tables[ ] = $t[ 0 ];
            }
        }
        return in_array( (string)$table, self::$exists_tables );
    }

    /**
     * Добавит созданную таблицу в кэш
     * @param Data_Table $table
     * @return void
     */
    private function addNewTable( Data_Table $table )
    {
        $this->getDB()->query( $this->table->getCreateTable() );
        self::$exists_tables[ ] = (string)$table;
    }

    /**
     * Вернет нужную модель
     * @static
     * @param  $class_name
     * @return \Sfcms_Model
     * @throws \RuntimeException
     */
    final static public function getModel( $class_name )
    {
        if ( ! $class_name ) {
            throw new \RuntimeException( sprintf('Model "%s" is not defined', $class_name) );
        }
        if ( ! preg_match('/^model_/i', $class_name) ) {
            $class_name = 'Model_' . $class_name;
        }
        if( ! isset( self::$all_class[ $class_name ] ) ) {
            if( class_exists( $class_name, true ) ) {
                self::$all_class[ $class_name ] = new $class_name();
            } else {
                throw new \RuntimeException( 'Model "' . $class_name . '" not found' );
            }
        }
        return self::$all_class[ $class_name ];
    }

    /**
     * Создать объект
     * @param array $data Массив инициализации объекта
     * @param bool $reFill Принудительно записать поля, если создается объект из массива, име.щего id
     * @return Data_Object
     */
    final public function createObject( $data = array(), $reFill = false )
    {
//        $start = microtime( 1 );
        // TODO Если создаем существующий объект, то св-ва не перезаписываем
        if( isset( $data[ 'id' ] ) && null !== $data[ 'id' ] && '' !== $data[ 'id' ] ) {
            $obj = $this->getFromMap( $data[ 'id' ] );
            if ( $obj ) {
                if ( $reFill ) $obj->attributes = $data;
                return $obj;
            }
        }
        $class_name = $this->objectClass();
        $obj = new $class_name( $this, $data );
        if( ! is_null( $obj->getId() ) ) {
            $this->addToMap( $obj );
            $obj->markClean();
        }
        //        print get_class($obj).'.'.$obj->getId().';'.round(microtime(1)-$start,3)."|\n";
        return $obj;
    }


    /**
     * Адаптер к наблюдателю для получения объекта
     * @param int $id
     * @return Data_Object
     */
    private function getFromMap( $id )
    {
        return Data_Watcher::exists( $this->objectClass(), $id );
    }

    /**
     * Адаптер к наблюдателю для добавления объекта
     * @param Data_Object $obj
     * @return void;
     */
    private function addToMap( Data_Object $obj )
    {
        Data_Watcher::add( $obj );
    }


    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_' . substr( get_class( $this ), 6 );
    }

    /**
     * Класс таблицы БД
     * @abstract
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_' . substr( get_class( $this ), 6 );
    }

    /**
     * Вернет таблицу модели
     * @return Data_Table
     */
    final public function getTable()
    {
        if( null === $this->table ) {
            $class_name = $this->tableClass();

            $this->table = new $class_name();

            if( $this->config->get( 'db.migration' ) ) {
                if( $this->isExistTable( $this->table ) ) {
                    $this->migration();
                } else {
                    $this->addNewTable( $this->table );
                    $this->onCreateTable();
                }
            }

            if ( $this->config->get('db.autoGenerateMeta') ) {
                $this->db->createMetaDataXML( (string) $this->table );
            }
        }

        return $this->table;
    }

    /**
     * Выполнение сверки таблицы и выполнение миграции полей
     * @return void
     */
    private function migration()
    {
        $sys_fields  = $this->getTable()->getFields();
        $have_fields = $this->getDB()->getFields( (string)$this->getTable() );

        $txtsys_fields = array();
        foreach( $sys_fields as $sfield ) {
            /** @var Data_Field $sfield */
            $txtsys_fields[ ] = $sfield->getName();
        }

        $add_array = array_diff( $txtsys_fields, $have_fields );
        $del_array = array_diff( $have_fields, $txtsys_fields );

        $sql = array();

        if( count( $add_array ) || count( $del_array ) ) {
            foreach( $del_array as $col ) {
                $sql[ ] = "ALTER TABLE `{$this->getTable()}` DROP COLUMN `$col`";
            }
            foreach( $add_array as $key => $col ) {
                $after = '';
                if( $key == 0 ) {
                    $after = ' FIRST';
                }
                if( $key > 0 ) {
                    $after = ' AFTER `' . $sys_fields[ $key - 1 ]->getName() . '`';
                }
                $sql[ ] = "ALTER TABLE `{$this->getTable()}` ADD COLUMN " . $sys_fields[ $key ] . $after;
            }

            foreach( $sql as $query ) {
                $this->getDB()->query( $query );
            }
        }
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
        return (string)$this->getTable();
    }

    /**
     * Установить связи для следующего запроса
     * @return Sfcms_Model
     */
    public function with()
    {
        if ( func_get_arg(0) && is_array( func_get_arg(0) ) ) {
            $this->with = func_get_arg(0);
        } else {
            $this->with = func_get_args();
        }
        return $this;
    }

    /**
     * Create criteria
     * @deprecated Need use createCriteria()
     * @param array $params
     * @return Db_Criteria
     */
    public function criteriaFactory( $params = array() )
    {
        trigger_error('Need use createCriteria()', E_DEPRECATED);
        return new Db_Criteria( $params );
    }

    /**
     * Create criteria
     * @param array $params
     * @return Db_Criteria
     */
    public function createCriteria( $params = array() )
    {
        return new Db_Criteria( $params );
    }

    /**
     * Finding data by primary key
     * @throws Sfcms_Model_Exception
     * @param int|array|string|Db_Criteria $crit
     * @param array $params
     * @return Data_Object
     */
    final public function find( $crit, $params = array() )
    {
        $this->with = array();
        $criteria   = null;
        if( is_object( $crit ) ) {
            if( $crit instanceof Db_Criteria ) {
                $criteria = new Data_Criteria( $this->getTable(), $crit );
            } elseif( $crit instanceof Data_Criteria ) {
                $criteria = $crit;
            }
        }
        // не определился критерий, но параметр - число
        // тогда полагаем, что параметр - это ID объекта
        if( null === $criteria && is_numeric( $crit ) ) {
            $obj = $this->getFromMap( $crit );
            if( $obj ) {
                return $obj;
            }
            $crit = array(
                'cond'  => 'id = :id',
                'params'=> array( ':id'=> $crit ),
                'limit' => '1',
            );
        } elseif( is_array( $crit ) ) {
            $default = array(
                'select'    => '*',
                'cond'      => 'id = :id',
                'params'    => array( ':id'=> 1 ),
                'limit'     => '1',
            );
            $crit   = array_merge( $default, $crit );
        } elseif ( is_string( $crit ) ) {
            $crit = array(
                'cond' => $crit,
                'params' => $params,
            );
        }

        if ( is_array( $crit ) )  {
            $crit = $this->createCriteria( $crit );
        }

        if ( ! is_object( $crit ) && ! $crit instanceof Db_Criteria ) {
            throw new Sfcms_Model_Exception( 'Not valid criteria' );
        }

        if( ! isset( $criteria ) && isset( $crit ) ) {
            $criteria = new Data_Criteria( $this->getTable(), $crit );
        }

        $data = $this->db->fetch( $criteria->getSQL(), DB::F_ASSOC, $crit->params );

        if( $data ) {
            $obj = $this->getFromMap( $data[ 'id' ] );
            if( null !== $obj ) {
                return $obj;
            } else {
                $obj_data = $this->createObject( $data );
                return $obj_data;
            }
        }
        return null;
    }

    /**
     * @param array|string $crit
     * @param array $params
     * @param string $order
     * @param string $limit
     * @return array|Data_Collection
     * @throws Sfcms_Model_Exception
     */
    final public function findAll( $crit = array(), $params = array(), $order = '', $limit = '' )
    {
        $with       = $this->with;
        $this->with = array();

        if( is_array( $crit ) || ( is_object( $crit ) && $crit instanceof Db_Criteria ) ) {
            $criteria = new Data_Criteria( $this->getTable(), $crit );
        } elseif( is_string( $crit ) && is_array( $params ) && '' != $crit ) {
            $criteria = new Data_Criteria( $this->getTable(),
                array(
                    'cond'  => $crit,
                    'params'=> $params,
                    'order' => $order,
                    'limit' => $limit,
                )
            );
        } elseif( is_object( $crit ) && $crit instanceof Data_Criteria ) {
            $criteria = $crit;
        } else {
            throw new Sfcms_Model_Exception( 'Not valid criteria' );
        }

        $raw = $this->db->fetchAll( $criteria->getSQL() );

        if( count( $raw ) ) {
            $collection = new Data_Collection( $raw, $this );
            if( count( $with ) ) {
                foreach( $with as $rel ) {
                    $relation = $this->getRelation( $rel, $collection->getRow(0) );
                    $relation->with( $collection );
                }
            }
        } else {
            $collection = new Data_Collection();
        }
        return $collection;
    }

    /**
     * Поиск по отношению
     * @param string $rel
     * @param mixed $obj
     * @return array|Data_Object|null
     */
    final public function findByRelation( $rel, Data_Object $obj )
    {
        $relation = $this->getRelation( $rel, $obj );
        if ( $relation ) {
            return $relation->find();
        }
        return null;
    }


    /**
     * Фабрика отношений
     * @param string      $rel
     * @param Data_Object $obj
     *
     * @return null|Data_Relation
     */
    private function getRelation( $rel, Data_Object $obj )
    {
        $relation = $obj->getModel()->relation();
        switch ( $relation[ $rel ][ 0 ] ) {
            case self::BELONGS:
                return new Data_Relation_Belongs( $rel, $obj );
            case self::HAS_ONE:
                return new Data_Relation_One( $rel, $obj );
            case self::HAS_MANY:
                return new Data_Relation_Many( $rel, $obj );
            case self::STAT:
                return new Data_Relation_Stat( $rel, $obj );
        }
        return null;
    }

    /**
     * Сохраняет данные модели в базе
     * @param Data_Object $obj
     * @return int
     */
    public function save( Data_Object $obj )
    {
        if( ! $this->onSaveStart( $obj ) ) {
            return false;
        }
//        $data      = $obj->attributes;
        $fields    = $this->getTable()->getFields();
//        $changed   = $obj->changed();
        $save_data = array();

//        $this->log( $obj->attributes, get_class($obj).'.'.$obj->getId() );

        /** @var Data_Field $field */
        foreach( $fields as $field ) {
            $val = $obj->get( $field->getName() );
            if( 'id' != $field->getName() && null !== $val ) {
                $save_data[ $field->getName() ] = $val;
            }
        }

        if ( ! count( $save_data ) ) {
            return null;
        }

        $ret = null;
        if( null !== $obj->getId() ) {
            $ret = $this->db->update( $this->getTableName(), $save_data, '`id` = ' . $obj->getId() );
        } else {
            $ret     = $this->db->insert( $this->getTableName(), $save_data );
            $obj->set('id', $ret);
            $this->addToMap( $obj );
        }
        if( null !== $ret ) {
            $this->onSaveSuccess( $obj );
        }
        return $ret;
    }

    /**
     * @param Data_Object $obj
     * @return boolean
     */
    public function onSaveStart( Data_Object $obj = null )
    {
        return true;
    }

    /**
     * Тут нельзя вызывать сохраниение объекта, или вызывать очень осторожно.
     * Иначе возникнет бесконечный цикл
     * @param Data_Object $obj
     * @return boolean
     */
    public function onSaveSuccess( Data_Object $obj = null )
    {
        return true;
    }

    /**
     * Удаляет строку из таблицы
     * @param int $id
     * @return boolean|mixed
     */
    final public function delete( $id )
    {
        if( $this->onDeleteStart( $id ) === false ) {
            return false;
        }

        $obj = $this->find( $id );
        if( $obj ) {
            Data_Watcher::del( $obj );
            if( $this->getDB()->delete( $this->getTableName(), 'id = :id', array( ':id'=> $obj->getId() ) ) ) {
                $this->onDeleteSuccess( $id );
                return true;
            }
        }
        return false;
    }

    /**
     * Событие, вызывается перед удалением объекта
     * Если вернет false, объект не будет удален
     * @param int $id
     * @return boolean
     */
    public function onDeleteStart( $id = null )
    {
        return true;
    }

    /**
     * Событие, вызывается после успешного удаления объекта
     * @param int $id
     * @return boolean
     */
    public function onDeleteSuccess( $id = null )
    {
        return true;
    }

    /**
     * Вернет количество записей по условию
     * @param string|Db_Criteria $cond
     * @param array $params
     * @return int
     */
    final public function count( $cond = '', $params = array() )
    {
        //$this->log( $cond, 'count' );
        if ( is_object( $cond ) && $cond instanceof Db_Criteria ) {
            $params = $cond->params;
            $cond   = $cond->condition;
        }

        $criteria = new Data_Criteria( $this->getTable(), array(
            'select'    => 'COUNT(*)',
            'cond'      => $cond,
            'params'    => $params,
        ) );

        $sql = $criteria->getSQL();

        $count = $this->db->fetchOne( $sql );

        if( $count ) {
            return $count;
        }

        return 0;
    }

    /**
     * Начало транзакции
     * @return void
     */
    public function transaction()
    {
        $pdo = $this->db->getResource();
        $pdo->beginTransaction();
    }

    /**
     * Применение транзакции
     * @return void
     */
    public function commit()
    {
        $pdo = $this->db->getResource();
        $pdo->commit();
    }

    /**
     * Откат транзакции
     * @return void
     */
    public function rollBack()
    {
        $pdo = $this->db->getResource();
        $pdo->rollBack();
    }

}