<?php
namespace Sfcms;

use ErrorException;
use Sfcms\LoggerInterface;
use SimpleXMLElement;

/**
 * Класс работы с MySQL
 */

class dbException extends Exception {};

/**
 * @author  Ermin Nikolay
 * @link    http://ermin.ru
 */
final class db
{
    // @TODO Убрать зависимости от системы.

    const   F_ASSOC = \PDO::FETCH_ASSOC;
    const   F_OBJ   = \PDO::FETCH_CLASS;
    const   F_ARRAY = \PDO::FETCH_NUM;
    const   F_XML   = 'sf_db_xml';

    static  $server = null;
    static  $login  = null;
    static  $password = null;
    static  $base   = null;

    private $data;

    /**
     * @var \PDOStatement
     */
    private $result    = null;

    /**
     * @var \PDO
     */
    private $resource  = null;

    public  $time   = 0;
    public  $count  = 0,
            $insert = 0,
            $update = 0,
            $delete = 0;
    private $tables = array();
    private $errno  = 0;
    private $error  = "";
    private $return;

    /**
     * Класс-логер
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Инстанс класса
     * @var db $instance
     */
    private static $instance = null;


    function __get( $key )
    {
        if ( $key == 'res' ) {
            throw new dbException("Do not using 'res', using 'result'");
        }
        if ( isset( $this->$key ) ) {
            return $this->$key;
        }
        else {
            return false;
        }
    }

    function __destruct()
    {
        //$this->saveLog();
    }

    /**
     * Устанавливает класс логгер
     * @param LoggerInterface $logger
     * @return void
     */
    function setLoggerClass(LoggerInterface $logger )
    {
        $this->logger   = $logger;
    }

    /**
     * Логирует запросы
     *
     * @param string $msg
     */
    protected function log($msg)
    {
        if (null !== $this->logger) {
            $this->logger->log($msg);
        }
    }

    /**
     * Принимает класс содержащий конфигурацию
     * @param array|\PDO $dbc
     *
     * @throws dbException
     */
    private function init($dbc = null)
    {
        if ($dbc instanceof \PDO) {
            $this->resource = $dbc;
        }

        if (is_array($dbc)) {
            if (isset($dbc['dsn'])) {
                $dsn = $dbc['dsn'];
            } else {
                $dsn = "mysql:host={$dbc['host']};dbname={$dbc['database']}";
            }
            $dbc['options'] = isset($dbc['options']) ? $dbc['options'] : array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );

            $this->resource = new \PDO($dsn, $dbc['login'], $dbc['password']);
            if (!$this->resource) {
                throw new dbException('Invalid connect to database');
            }
            $this->resource->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->resource->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, \PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * Создает соединение с базой
     */
    public function __construct($config = array()) {
        $this->init($config);
        self::$instance = $this;
    }


    /**
     * Вернет ссылку на объект базы данных
     * @param array $config
     *
     * @return null|db
     */
    static function getInstance($config = array())
    {
        if (null === self::$instance) {
            self::$instance = new db($config);
        }
        return self::$instance;
    }

    /**
     * Ресурс \PDO
     * @return \PDO
     */
    function getResource()
    {
        return $this->resource;
    }

    public function beginTransaction()
    {
        $this->log(__FUNCTION__);
        $this->resource->beginTransaction();
    }

    public function rollBack()
    {
        $this->log(__FUNCTION__);
        $this->resource->rollBack();
    }

    public function commit()
    {
        $this->log(__FUNCTION__);
        $this->resource->commit();
    }

    /**
     * Выполняет запрос к БД. Полезно для UPDATE, DELETE
     *
     * @param string $sql
     * @return mixed
     */
    function query($sql)
    {
        $start = microtime(true);
        $sql = trim( $sql );

        try {
            $this->return = $this->resource->exec( $sql );
        } catch ( Exception $e ) {
            $this->log($sql);
            $this->log($e->getMessage());
            throw $e;
        }


        if ( $this->return === false ) {
            $error = $this->resource->errorInfo();
            throw new dbException( $error[2] );
        }

        //$this-> = "";
        if (preg_match( '/^(\w+)/u', $sql, $m )) {
            switch ($m[1]) {
                case 'UPDATE':
                    $this->update++;
                    break;
                case 'INSERT':
                    $this->insert++;
                    $this->return   = $this->resource->lastInsertId();
                    break;
                case 'DELETE':
                    $this->delete++;
                    break;
                case 'CREATE':
                    $this->count++;
                    break;
                default:
                    $this->count++;
            }
        }


        $exec = round(microtime(true) - $start, 4);
        $this->log( $sql . " ({$exec} sec)" );
        $this->time += $exec;

        $error = $this->resource->errorInfo();
        $this->errno = 0;
        $this->error = '';
        if ( isset($error[1]) && isset($error[2]) ) {
            $this->errno = $error[1];
            $this->error = $error[2];
        }

        return $this->return;
    }

    /**
     * Удалит ряд из таблицы
     *
     * $db->delete( 'my_table', 'id = 5' );
     *
     * @param $table
     * @param $where
     * @return mixed
     */
    function delete( $table, $where, $values = array() )
    {
        if ( count($values) ) {
            foreach( $values as $k => $v ) {
                $v = $this->escape( $v );
                $k  = $k{0} == ':' ? $k : ':'.$k;
                $where = str_replace($k, $v, $where);
            }
        }

        $this->result = $this->query("DELETE FROM `{$table}` WHERE {$where}");
        if ( !$this->errno ) {
            return $this->result;
        }
        else {
            return false;
        }
    }

    /**
     * Подготовит SQL запрос
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public function prepare($sql, array $params)
    {
        try {
            $this->result = $this->resource->prepare($sql);
            $this->result->execute($params);
        } catch ( \PDOException $e ) {
            $this->logger->error($sql, $params);
            $this->logger->error($e->getMessage(), array($e->getTrace()));
            return null;
        }

        return $this->result;
    }

    /**
     * Возвращает первую строку результата запроса
     *
     * @param string $sql
     * @param int $extract
     * @param array $params
     * @return array
     */
    public function fetch( $sql, $extract = db::F_ASSOC, array $params = array() )
    {
        $start  = microtime(1);

        $command    = substr($sql, 0, strpos($sql, ' '));
        if ( ! in_array( $command, array('SELECT','SHOW') ) ) {
            throw new Exception('DB: Using FETCH no for SELECT');
        }

        $this->prepare( $sql, $params );

        $num_rows = $this->result->columnCount();

        if ( $this->result && $num_rows ) {

            $data = $this->result->fetch($extract);
            if ( $extract == db::F_XML ) {
                $xml = new SimpleXMLElement('<data></data>');
                foreach ( $data as $n => $v ) {
                    $field = $xml->addChild('field', $v);
                    $field->addAttribute('name', $n);
                }
                $data   = $xml->asXML();
            }

            $exec = round(microtime(1)-$start, 4);
            $this->log( $sql." ($exec sec)" );
            $this->time += $exec;

            return $data;
        }
        else {
            return false;
        }
    }

    /**
     * Возвращает все строки результата запроса
     *
     * @param string $sql
     * @param bool $index_id|array $params
     * @param int $extract
     * @param array $params
     * @return array in array
     */
    function fetchAll( $sql, $index_id = false, $extract = self::F_ASSOC, array $params = array() )
    {
        $start  = microtime(1);
        $sql    = trim( $sql );
        $command    = substr($sql, 0, strpos($sql, ' '));

        if ( is_array( $index_id ) ) {
            $params = $index_id;
            $index_id = false;
            $extract = self::F_ASSOC;
        }

        if ( ! in_array( $command, array('SELECT','SHOW') )) {
            throw new dbException('Использование fetchAll не для SELECT или SHOW');
        }

        $this->prepare( $sql, $params );

        $num_rows = $this->result->columnCount();

        if ( $this->result && $num_rows ) {

            $all_data = $this->result->fetchAll( $extract );

            $indexed_data = array();

            if ( $extract == db::F_XML ) {
                $xml = new SimpleXMLElement('<alldata></alldata>');
                foreach ( $all_data as $data ) {
                    $xdata = $xml->addChild('data');
                    foreach ( $data as $n => $v ) {
                        $field = $xdata->addChild('field', $v);
                        $field->addAttribute('name', $n);
                    }
                }
                $all_data   = $xml->asXML();
            } else {
                if ( $index_id ) {
                    foreach ( $all_data as $data ) {
                        if ( $extract == self::F_OBJ ) {
                            $index  = $data->id;
                        }
                        elseif (isset( $data['id'] )) {
                            $index  = $data['id'];
                        } elseif ( isset( $data[0] ) ) {
                            $index  = $data[0];
                        }
                        else {
                            $index  = null;
                        }
                        $indexed_data[ $index ] = $data;
                    }
                    $all_data = $indexed_data;
                }
            }
            $exec = round(microtime(1)-$start, 4);
            $this->log( $sql." ($exec sec)" );
            $this->time += $exec;
            return $all_data;
        }
        return false;
    }

    /**
     * Step fetching
     * @param int $extract
     * @return mixed
     */
    protected function fetchStep( $extract = self::F_ASSOC )
    {
        $this->data = false;
        if ( $this->result ) {
            $this->data = $this->result->fetch($extract);
        }
        return $this->data;
    }

    /**
     * Вернет первую ячейку первой записи результата запроса
     *
     * @param string $sql
     * @return string
     */
    function fetchOne( $sql, array $params = array() )
    {
        $start = microtime(1);
        if (substr(trim($sql), 0, 6) != 'SELECT') {
            throw new dbException('Using fetchOne not for SELECT');
        }

        $this->prepare( $sql, $params );

        $num_rows = $this->result->columnCount();

        if ( $this->result && $num_rows ) {
            $data = $this->fetchStep( self::F_ARRAY );
            $this->data = $data;

            $exec = round(microtime(1) - $start, 4);
            $this->time+= $exec;
            $this->log( $sql." ($exec sec)" );
            return $this->data[0];
        } else {
            return false;
        }
    }

    /**
     * Вернет количество записей таблице $table по условию $where
     *
     * $db->count( DBTABLE, 'hidden = 0' );
     *
     * @param $table
     * @param $where
     * @return integer
     */
    function count( $table, $where = '' )
    {
        $start = microtime(1);
        if ( $where != '' ) {
            $where  = ' WHERE '.$where;
        }
        $sql    = 'SELECT COUNT(*) FROM `'.$table.'` '.$where;
        $count = $this->fetchOne($sql);
        $exec = round(microtime(1) - $start, 4);
        $this->time+= $exec;
        $this->log( $sql." ($exec sec)" );
        return $count;
    }


    /**
     * Вставляет значения в базу
     *
     * $db->insert( DBTABLE, array('id'=>'', 'name'=>'vasya', 'comment'=>'hello') );
     *
     * @param string $db таблица БД
     * @param array $data вставляемые значения
     * @return integer номер новой записи
     */
    function insert($table, $data)
    {
        // проверка на апостроф
        foreach ($data as $i => $d) {
            $data[$i] = $this->resource->quote( $d );
        }

        $datas = join ( ",", $data );
        if (isset($data[0])) {
            $keys = '';
        } else {
            $keys = "(`" . join ( "`,`", array_keys( $data ) ) . "`)";
        }
        $sql = "INSERT INTO `{$table}` {$keys} VALUES ($datas)";
        $this->query( $sql );
        if ( $this->result ) {
            return $this->return;
        }
        return false;
    }

    /**
     * Выполняет обновление в Базе Данных
     *
     * $db->update( DBTABLE,
     *     array('name'=>'vasya', 'comment'=>'hello world'),
     * 'id = 10', 1);
     *
     * @param string $table
     * @param array $data
     * @param string $where
     * @param int $limit
     */
    function update($table, $data, $where = '', $limit = 1)
    {
        if ( trim( $limit ) ) {
            $limit = ' LIMIT ' . $limit;
        }

        // проверка на апостроф
        $data = array_map( array($this->resource, 'quote'), $data );
//        foreach ($data as $i => $d) {
//            $data[ $i ] = $this->resource->quote( $d );
//        }

        $set = array ();
        if (is_array ( $data ) && count ( $data )) {
            foreach ( $data as $k => $d ) {
                $set[] = "`$k` = $d";
            }
        }

        if (strlen($where) && substr($where, 0, 5) != 'WHERE') {
            $where = 'WHERE '.$where;
        }

        $sql = "UPDATE `{$table}` SET " . join(',', $set) .' '. $where . $limit;

        $this->query( $sql );

        return $this->return;
    }

    /**
     * Производит INSERT .. ON DUPLICATE KEY UPDATE запрос
     *
     * @param string $table
     * @param array $var_list
     * @param array $upd_list
     * @return int/bool
     */
    function insertUpdate( $table, $var_list, $upd_list = array() )
    {

        if ( count( $upd_list ) && count( $upd_list ) != count( $var_list ) ) {
            throw new dbException("Not the same length arrays in arguments");
        }

        // проверка на апостроф
        foreach ($var_list as $i => $d) {
            $var_list[ $i ] = $this->resource->quote( $d );
        }

        /*
          INSERT INTO <table> ( <list fields> ) VALUES ( <list values> )
          ON DUPLICATE KEY UPDATE
              field1 = VALUES( field1 ),
              field2 = VALUES( field2 )
          */

        $list_fields = '`'.join( '`,`', array_keys($var_list) ).'`';
        $list_values = join(',', $var_list );

        // заполняем список значений, которые надо изменить
        if (  count( $upd_list ) == 0 ) {
            $upd_list = array_keys( $var_list );
        }

        $upd = array();
        foreach ( $upd_list as $u ) {
            $upd[] = " `{$u}` = VALUES( `{$u}` ) ";
        }
        $upd_str = join(',', $upd);

        $sql = "INSERT INTO `{$table}` ( $list_fields )
                VALUES ( $list_values )
                ON DUPLICATE KEY UPDATE {$upd_str}";
        if ( $this->query( $sql ) ) {
            return $this->return;
        }
        return false;
    }

    /**
     * Производит многострочный INSERT .. ON DUPLICATE KEY UPDATE запрос
     *
     * @param string $table
     * @param array $var_list
     * @param array $upd_list
     * @return int/bool
     */
    function insertUpdateMulti( $table, $var_list, $upd_list=false )
    {
        /*
          INSERT INTO <table> ( <list fields> ) VALUES ( <list values[0]> ),... ,( <list values[n]> )
          ON DUPLICATE KEY UPDATE
              field1 = VALUES( field1 ),
              field2 = VALUES( field2 )
          */
        $list_value = array();
        foreach ($var_list as $j => $row) {
            // проверка на апостроф
            foreach ($row as $i => $d) {
                $row[ $i ] = $this->resource->quote( $d );
            }

            $list_value[] = '('.join(',', $row ).')';
        }

        $list_fields = '`'.join( '`,`', array_keys($var_list[0]) ).'`';
        $list_values = join(',', $list_value);

        $upd = array();
        if ( $upd_list ) {
            foreach ( $upd_list as $u ) {
                $upd[] = " `{$u}` = VALUES( `{$u}` ) ";
            }
        } else {
            foreach ( array_keys( $var_list[0] ) as $u ){
                if ( $u != 'id' ) {
                    $upd[] = " `{$u}` = VALUES( `{$u}` ) ";
                }
            }
        }
        $upd_str = join(',', $upd);

        $sql = "INSERT INTO `{$table}` ( {$list_fields} ) VALUES {$list_values}
                ON DUPLICATE KEY UPDATE {$upd_str}";

        //print $sql;
        $this->query( $sql );
        return $this->return;

    }

    public function createMetaDataXML( $table )
    {
        $start = microtime(true);

        $this->result   = $this->resource->prepare("SHOW COLUMNS FROM `$table`");

        $xml = new DOMDocument('1.0','utf8');
        $xml->appendChild( $xmlTable = $xml->createElement('table') );
        $xmlTable->setAttribute('name', $table);
        $xmlFields = $xmlTable->appendChild( $xml->createElement('fields') );
        $xmlKeys = $xmlTable->appendChild( $xml->createElement('keys') );

        if ( ! $this->result->execute() ) {
            throw new ErrorException('Result Fields Query not valid');
        }

        foreach ( $this->result->fetchAll(\PDO::FETCH_OBJ) as $field  ) {
            $xmlFields->appendChild( $xmlField = $xml->createElement('field',$field->Default) );
            $xmlField->setAttribute('name',$field->Field);
            $xmlField->setAttribute('type',$field->Type);
            $xmlField->setAttribute('null',$field->Null);
        }

        $this->result = $this->resource->prepare("SHOW KEYS FROM `$table`");

        if ( ! $this->result->execute() ) {
            throw new ErrorException('Result Keys Query not valid');
        }

        foreach ( $this->result->fetchAll( \PDO::FETCH_OBJ ) as $key ) {
            $xmlKeys->appendChild( $xmlKey = $xml->createElement('key') );
            $xmlKey->setAttribute( 'column', $key->Column_name );
            $xmlKey->setAttribute( 'key', $key->Key_name );
            $xmlKey->setAttribute( 'type', $key->Index_type );
        }

        $exec = round(microtime(true)-$start, 4);
        $this->time += $exec;
        $this->log( "SHOW COLUMNS FROM `$table`"." ($exec sec)" );
        $xml->formatOutput = true;
        $path = ROOT.'/_runtime/model';
        if ( ! file_exists( $path ) ) {
            mkdir( $path, 0775, true );
        } elseif ( ! is_writable( $path ) ) {
            throw new ErrorException("Path '$path' is not writable");
        }
        $xml->save( $path .'/'. $table.'.xml' );
    }

    /**
     * Экранирует символы
     * @param $str
     * @return string
     */
    function escape( $str )
    {
        return $this->resource->quote( $str );
    }

}

