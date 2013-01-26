<?php
namespace Sfcms;

/**
 * Интерфейс модели
 */

use App;
use Module\System\Model\LogModel;
use Sfcms\Component;
use Sfcms\Data\Collection;
use Sfcms\Data\Query\Builder as QueryBuilder;
use Sfcms\Model\Plugin;
use Sfcms\db;
use Sfcms\Db\Criteria;
use RuntimeException;
use PDO;
use Module\System\Object\User;
use Sfcms\Data\Table;
use Sfcms\Form\Form;
use Sfcms\Data\Object;
use Sfcms\Data\Watcher;
use Sfcms\Data\Field;
use Sfcms\Data\Relation;

abstract class Model extends Component
{
    const HAS_MANY      = 'has_many'; // содержет много
    const ONE_TO_MANY   = 'has_many'; // содержет много
    const HAS_ONE       = 'has_one'; // содержет один
    const ONE_TO_ONE    = 'has_one'; // содержет один
    const BELONGS       = 'belongs'; // принадлежит
    const MANY_TO_ONE   = 'belongs'; // принадлежит

    const STAT          = 'stat'; // статистическая связь

    /**
     * Тип таблицы
     * @var string
     */
    //protected $engine   = 'MyISAM';
    protected $engine   = 'InnoDB';

    /**
     * @var db
     */
    protected $db;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Table
     */
    protected $table = null;

    /**
     * @var Form
     */
    //protected $form;

    /**
     * @var Object
     */
    protected $data;

    /**
     * Список полей
     * @var array
     */
    protected $fields = array();

    /**
     * Список инстанцированных классов модели
     * @var array
     */
    protected static $all_class = array();

    protected static $exists_tables;

    protected static $dao;

    /** @var array Список моделей, доступных в системе */
    protected static $models = null;

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
     * @param $name
     * @param Object $obj
     */
    protected function callPlugins( $name, Object $obj )
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
     * @param Plugin $plugin
     * @param $namespace
     */
    protected function addPlugin( Plugin $plugin, $namespace = 'default' )
    {
        $this->_plugins[$namespace][] = $plugin;
    }


    /**
     * Отношения модели с другими моделями
     *
     * <p>Пример:</p>
     * <pre>array(
     *     'Category' => array(self::BELONGS, 'CategoryModel', 'category_id', 'order'=>'date', 'limit'=>20),
     * );</pre>
     * <ul>
     *     <li>Первый параметр - тип отношения</li>
     *     <li>Второй параметр - модель, с помощью которой будет происходить поиск</li>
     *     <li>Третий параметр - ключ, по которому будет происходить поиск</li>
     *     <li>order и limit   - Дополнительные параметры</li>
     * </ul>
     * @return array
     */
    public function relation()
    {
        return array();
    }

    /**
     * Проверяет существование таблицы
     * @param string $table
     * @return boolean
     */
    private function isExistTable( $table )
    {
        if( ! isset( self::$exists_tables ) ) {
            self::$exists_tables = array();
            $tables = $this->db->fetchAll( "SHOW TABLES", false, DB::F_ARRAY );
            foreach( $tables as $t ) {
                self::$exists_tables[ ] = $t[ 0 ];
            }
        }
        return in_array( $table, self::$exists_tables );
    }

    /**
     * Добавит созданную таблицу в кэш
     * @param string $table
     * @return void
     */
    private function addNewTable( $table )
    {
        $this->getDB()->query( $this->table->getCreateTable() );
        self::$exists_tables[ ] = $table;
    }

    /**
     * Вернет нужную модель
     * @static
     * @param  $model
     * @return self
     * @throws RuntimeException
     */
    final static public function getModel( $model )
    {
        $class_name = $model;
        // Если нет в кэше и указан не абсолютный путь
        if ( ! isset( self::$all_class[ $model ] ) && false === strpos( $class_name, '\\') ) {
            // Если указан псевдоним
            // Псевдонимом считается класс, не имеющий символов \ и _
            if ( null === self::$models ) {
                self::$models = App::getInstance()->getModels();
            }
            $modelKey = strtolower( $class_name );
            if ( isset( self::$models[ $modelKey ] ) ) {
                $class_name = self::$models[ $modelKey ];
            }
        }
//        \App::getInstance()->getLogger()->log($class_name,__FUNCTION__);
        if( ! isset( self::$all_class[ $model ] ) ) {
            if( class_exists( $class_name, true ) ) {
                self::$all_class[ $model ] = new $class_name();
            } else {
                throw new RuntimeException( sprintf('Model "%s" not found',$class_name) );
            }
        }
        return self::$all_class[ $model ];
    }

    /**
     * Создать объект
     * @param array $data Массив инициализации объекта
     * @param bool $reFill Принудительно записать поля, если создается объект из массива, име.щего id
     * @return Object
     */
    final public function createObject( $data = array(), $reFill = false )
    {
//        $start = microtime( 1 );
//        debugVar(  $this->objectClass().'.'.$data['id'], __FUNCTION__ );
        // TODO Если создаем существующий объект, то св-ва не перезаписываем
        if( isset( $data[ 'id' ] ) && null !== $data[ 'id' ] && '' !== $data[ 'id' ] ) {
            $obj = $this->getFromMap( $data[ 'id' ] );
            if ( $obj ) {
                if ( $reFill ) {
                    $obj->attributes = $data;
                }
                return $obj;
            }
        }
        $class_name = $this->objectClass();
        /** @var $obj Object */
        $obj = new $class_name( $this, $data );
        if( null !== $obj->id ) {
            $this->addToMap( $obj );
            $obj->markClean();
        }
        //        print get_class($obj).'.'.$obj->getId().';'.round(microtime(1)-$start,3)."|\n";
        return $obj;
    }


    /**
     * Адаптер к наблюдателю для получения объекта
     * @param int $id
     * @return Object
     */
    private function getFromMap( $id )
    {
        return Watcher::exists( $this->objectClass(), $id );
    }

    /**
     * Адаптер к наблюдателю для добавления объекта
     *
     * @param Object $obj
     */
    private function addToMap( Object $obj )
    {
        Watcher::add( $obj );
    }


    /**
     * Класс для сущности доменного объекта
     * @return string
     */
    public function objectClass()
    {
        return str_replace( array('\Model','Model'), array('\Object',''), get_class( $this ) );
    }

    /**
     * Класс таблицы БД
     * @abstract
     * @return string
     */
    public function tableClass()
    {
        return $this->objectClass();
    }

    /**
     * Вернет таблицу модели
     * @return string
     */
    final public function getTable()
    {
        if( null === $this->table ) {
            $class = $this->tableClass();

            $this->table = $class::getTable();

//            App::getInstance()->getLogger()->log($class_name,__FUNCTION__);

            if( $this->config->get( 'db.migration' ) ) {
                if( $this->isExistTable( $this->table ) ) {
                    $this->migration();
                } else {
                    $this->addNewTable( $this->table );
                    $this->onCreateTable();
                }
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
        $class = $this->objectClass();
        $sys_fields  = $class::getFields();
        $table = $class::getTable();
        $have_fields = $this->getDB()->getFields( $this->getTable() );

        $txtsys_fields = array();
        foreach( $sys_fields as $sfield ) {
            /** @var Field $sfield */
            $txtsys_fields[ ] = $sfield->getName();
        }

        $add_array = array_diff( $txtsys_fields, $have_fields );
        $del_array = array_diff( $have_fields, $txtsys_fields );

        $sql = array();

        if( count( $add_array ) || count( $del_array ) ) {
            foreach( $del_array as $col ) {
                $sql[ ] = "ALTER TABLE `{$table}` DROP COLUMN `$col`";
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
     * Установить связи для следующего запроса
     * @return Model
     */
    public function with()
    {
        if ( 1 == func_num_args() && func_get_arg(0) && is_array( func_get_arg(0) ) ) {
            $this->with = func_get_arg(0);
        }
        if ( func_num_args() >= 1 && is_string( func_get_arg(0) ) ) {
            $this->with = func_get_args();
        }
        return $this;
    }

    /**
     * Create criteria
     * @param array $params
     * @return Criteria
     */
    public function createCriteria( $params = array() )
    {
        return new Criteria( $params );
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function createCollection( array $data = null )
    {
        return new Collection( $data, $this );
    }

    /**
     * Finding data by primary key
     *
     * @param int|array|string|Criteria $crit
     * @param array $params
     *
     * @return Object
     * @throws Exception
     */
    final public function find( $crit, $params = array() )
    {
        $this->with = array();
        $criteria   = null;
        if( is_object( $crit ) ) {
            if( $crit instanceof Criteria ) {
                $criteria = new QueryBuilder( $this->objectClass(), $crit );
            } elseif( $crit instanceof QueryBuilder ) {
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

        if ( ! is_object( $crit ) && ! $crit instanceof Criteria ) {
            throw new Exception( 'Not valid criteria' );
        }

        if( ! isset( $criteria ) && isset( $crit ) ) {
            $criteria = new QueryBuilder( $this->objectClass(), $crit );
        }

        $data = $this->db->fetch( $criteria->getSQL(), db::F_ASSOC, $crit->params );

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
     * @return array|Collection
     * @throws Exception
     */
    final public function findAll( $crit = array(), $params = array(), $order = '', $limit = '' )
    {
        $with       = $this->with;
        $this->with = array();

        if( is_array( $crit ) || ( is_object( $crit ) && $crit instanceof Criteria ) ) {
            $criteria = new QueryBuilder( $this->objectClass(), $crit );
        } elseif( is_string( $crit ) && is_array( $params ) && '' != $crit ) {
            $criteria = new QueryBuilder( $this->objectClass(),
                array(
                    'cond'  => $crit,
                    'params'=> $params,
                    'order' => $order,
                    'limit' => $limit,
                )
            );
        } elseif( is_object( $crit ) && $crit instanceof QueryBuilder ) {
            $criteria = $crit;
        } else {
            throw new Exception( 'Not valid criteria' );
        }

//        if ( $criteria-> )

        $raw = $this->db->fetchAll( $criteria->getSQL() );

        if( count( $raw ) ) {
            $collection = new Collection( $raw, $this );
            if( count( $with ) ) {
                foreach( $with as $rel ) {
                    $relation = $this->getRelation( $rel, $collection->getRow(0) );
                    $relation->with( $collection );
                }
            }
        } else {
            $collection = new Collection();
        }
        return $collection;
    }

    /**
     * Поиск по отношению
     * @param string $rel
     * @param mixed $obj
     * @return array|Object|null
     */
    final public function findByRelation( $rel, Object $obj )
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
     * @param Object $obj
     *
     * @return null|Relation
     * @throws \InvalidArgumentException
     */
    private function getRelation( $rel, Object $obj )
    {
        $relation = $this->relation();

        if ( ! is_string( $rel ) ) {
//            $this->log($rel,'rel');
            throw new \InvalidArgumentException('Argument `rel` is not a string');
        }

        switch ( $relation[ $rel ][ 0 ] ) {
            case self::BELONGS:
                return new Relation\Belongs( $rel, $obj );
            case self::HAS_ONE:
                return new Relation\One( $rel, $obj );
            case self::HAS_MANY:
                return new Relation\Many( $rel, $obj );
            case self::STAT:
                return new Relation\Stat( $rel, $obj );
        }
        return null;
    }

    /**
     * Сохраняет данные модели в базе
     * @param Object $obj
     * @return int
     */
    public function save( Object $obj )
    {
        if( ! $this->onSaveStart( $obj ) ) {
            return false;
        }
//        $data      = $obj->attributes;
        $class     = $this->objectClass();
        $fields    = $class::getFields();
//        $changed   = $obj->changed();
        $save_data = array();

//        $this->log( $obj->attributes, get_class($obj).'.'.$obj->getId() );

        /** @var Field $field */
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
            $ret = $this->db->update( $this->getTable(), $save_data, '`id` = ' . $obj->getId() );
        } else {
            $ret     = $this->db->insert( $this->getTable(), $save_data );
            $obj->set('id', $ret);
            $this->addToMap( $obj );
        }
        if( null !== $ret ) {
            $this->onSaveSuccess( $obj );
        }
        return $ret;
    }

    /**
     * @param Object $obj
     * @return boolean
     */
    public function onSaveStart( Object $obj = null )
    {
        return true;
    }

    /**
     * Тут нельзя вызывать сохраниение объекта, или вызывать очень осторожно.
     * Иначе возникнет бесконечный цикл
     * @param Object $obj
     * @return boolean
     */
    public function onSaveSuccess( Object $obj = null )
    {
        // Записываем все события в таблицу log
        if ( $this->config->get('db.log') ) {
            /** @var $logModel LogModel */
            $logModel = $this->getModel('Module\\System\\Model\\LogModel');
            $this->getDB()->insert($logModel->getTable(),
                array(
                    'user'      => $this->app()->getAuth()->currentUser()->getId() ?: 0,
                    'object'    => get_class( $obj ),
                    'action'    => 'save',
                    'timestamp' => time(),
                )
            );
        }
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
            Watcher::del( $obj );
            if( $this->getDB()->delete( $this->getTable(), '`id` = :id', array( ':id'=> $obj->getId() ) ) ) {
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
     * @param string|Criteria $cond
     * @param array $params
     * @return int
     */
    final public function count( $cond = '', $params = array() )
    {
        //$this->log( $cond, 'count' );
        if ( is_object( $cond ) && $cond instanceof Criteria ) {
            $params = $cond->params;
            $cond   = $cond->condition;
        }

        $criteria = new QueryBuilder( $this->objectClass(), array(
            'select'    => 'COUNT(`id`)',
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