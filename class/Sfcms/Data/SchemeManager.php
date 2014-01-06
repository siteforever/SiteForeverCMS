<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Data;

use Module\Database\Event\DatabaseEvent;
use Sfcms\db;
use Sfcms\Model;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SchemeManager extends ContainerAware
{
    protected $existsTables;

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event.dispatcher');
    }

    /**
     * @return db
     */
    public function getDb()
    {
        return $this->container->get('db');
    }

    /**
     * @param Model $model
     * @return array
     */
    public function migrate(Model $model)
    {
        $table = $model->getTable();
        $event = new DatabaseEvent();

        if ($this->isExistTable($table)) {
            $sql = $this->getSqlForDifferenceTable($model);
        } else {
            $sql = $this->getSqlForCreateTable($model);
            $this->getEventDispatcher()->dispatch(DatabaseEvent::TABLE_CREATE, $event);
        }
        return (array) $sql;
    }

    /**
     * Проверяет существование таблицы
     * @param string $table
     *
     * @return boolean
     */
    public function isExistTable($table)
    {
        if (!isset($this->existsTables)) {
            $this->existsTables = array();
            $tables = $this->getDB()->fetchAll('SHOW TABLES', false, DB::F_ARRAY);
            foreach ($tables as $t) {
                $this->existsTables[] = $t[0];
            }
        }

        return in_array($table, $this->existsTables);
    }

    /**
     * Построение запроса для создания таблицы
     *
     * @param Model $model
     * @return string
     * @throws Exception
     */
    public function getSqlForCreateTable(Model $model)
    {
        $result = array(sprintf('CREATE TABLE `%s` (', $model->getTable()));

        $objectClass = $model->objectClass();
        $fields = call_user_func(array($objectClass, 'fields'));
        $pk     = call_user_func(array($objectClass, 'pk'));

        $params = array_map(function ($field) {
                /** @var Field $field */
                return $field->toString();
            }, $fields);

        if ($pk) {
            if (is_array($pk)) {
                $pk = '`' . join('`,`', $pk) . '`';
            } else {
                $pk = "`" . str_replace(',', '`,`', $pk) . "`";
            }
            $params[] = sprintf('PRIMARY KEY (%s)', $pk);
        }

        foreach (call_user_func(array($objectClass, 'keys')) as $key => $val) {
            $found = false;
            if (is_array($val)) {
                foreach ($val as $v) {
                    $subFound = false;
                    foreach ($fields as $field) {
                        /** @var $field Field */
                        if ($field->getName() == $v) {
                            $subFound = true;
                            break;
                        }
                    }
                    $found = $found || $subFound;
                }
                $val = implode(',', $val);
            } else {
                foreach ($fields as $field) {
                    /** @var $field Field */
                    if ($field->getName() == $val) {
                        $found = true;
                        break;
                    }
                }
            }

            if (!$found) {
                throw new Exception(sprintf('Key column "%s" doesn`t exist in table', $val));
            }

            $val = str_replace(',', '`,`', $val);
            if (is_numeric($key)) {
                $key = $val;
            }
            $params[] = sprintf('KEY `%s` (`%s`)', $key, $val);
        }

        $result[] = join(',' . PHP_EOL, $params);

        $result[] = sprintf(') ENGINE=%s DEFAULT CHARSET=utf8;', call_user_func(array($objectClass, 'getEngine')));

        return join(PHP_EOL, $result);
    }

    /**
     * Построение запросов для разности таблицы
     * @param Model $model
     * @return array
     */
    private function getSqlForDifferenceTable(Model $model)
    {
        $class = $model->objectClass();
        $sysFields  = call_user_func(array($class, 'fields'));
        $table       = call_user_func(array($class, 'table'));
        $haveFields = $this->getFields($table);

        $txtSysFields = array_map(
            function (Field $field) {
                return $field->getName();
            },
            $sysFields
        );

        $addArray = array_diff($txtSysFields, $haveFields);
        $removeArray = array_diff($haveFields, $txtSysFields);

        $sql = array();

        if (count($addArray) || count($removeArray)) {
            foreach ($removeArray as $col) {
                $sql[] = sprintf("ALTER TABLE `%s` DROP COLUMN `%s`;", $table, $col);
            }
            foreach ($addArray as $key => $col) {
                $after = '';
                if ($key == 0) {
                    $after = ' FIRST';
                }
                if ($key > 0) {
                    $after = sprintf(' AFTER `%s`', $sysFields[$key - 1]->getName());
                }
                $sql[] = sprintf("ALTER TABLE `%s` ADD COLUMN %s %s;", $table, $sysFields[$key], $after);
            }
        }
        return $sql;
    }

    /**
     * Вернет список полей
     *
     * @param string $table
     *
     * @return array
     * @throws \ErrorException
     */
    protected function getFields($table)
    {
        $pdo   = $this->getDB()->getResource();
        $sql = sprintf('SHOW COLUMNS FROM `%s`', $table);
        $result = $pdo->prepare($sql);

        $fields = array();

        if (!$result->execute()) {
            throw new \ErrorException('Result Fields Query not valid');
        }

        foreach ($result->fetchAll(\PDO::FETCH_OBJ) as $field) {
            $fields[] = $field->Field;
        }

        return $fields;
    }
}
