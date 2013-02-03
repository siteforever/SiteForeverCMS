<?php
/**
 * Интервейс контейнера для данных
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Sfcms\Data;

use Sfcms\Component;
use Sfcms\Model;

abstract class Object extends Table
{
    /**
     * @var Model
     */
    protected $model  = null;

    /**
     * @var Table
     */
    protected $table  = null;

    /**
     * @var array|null
     */
    protected $relation = null;

    /**
     * Список полей, которые были изменены
     * @var array
     */
    protected $changed = array();

    private $new = true;

    /**
     * @param Model $model
     * @param array $data
     */
    public function __construct( Model $model, $data )
    {
        $this->model    = $model;
        $this->table    = $model->getTable();
        $this->relation = $this->model->relation();

        $this->new = empty( $data['id'] );
        $this->setAttributes( $data );
        $this->new = false;

        // @TODO Пока не будем помечать новые объекты для добавления
        /*
         * Сделать несколько состояний для объектов:
         *   - Совсем новый   - просто заглушка
         *   - Новый для базы - будет сохранен в базу
         *   - Существующий   - загружен из базы
         */
        /*if ( is_null( $this->getId() ) ) {
            $this->markNew();
        }*/
    }

    /**
     * Вернет список измененных полей
     * @return array
     */
    public function changed()
    {
        return $this->changed;
    }

    /**
     * @param $key
     * @return array|Object|mixed|null
     */
    public function get( $key )
    {
        if ( isset( $this->relation[ $key ] ) ) {
            return $this->model->findByRelation( $key, $this );
        }
        return parent::get( $key );
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|Component
     * @throws \UnexpectedValueException
     */
    public function set( $key, $value )
    {
        $oldValue = isset( $this->data[$key] ) ? $this->data[$key] : null;
        parent::set( $key, $value );

        if ( 'id' == $key && $oldValue && $value && $oldValue != $value ) {
            throw new \UnexpectedValueException('Changing id is not allowed');
        }

        if ( null === $oldValue || $oldValue != $value ) {
            //if ( ! $this->new ) {
                //$this->changed[ $key ] = $key;
            $event = 'onSet'.ucfirst( strtolower( $key ) );
            if ( method_exists( $this, $event ) ) {
                $this->$event();
            }
            //}
            if ( empty( $this->data['id'] ) ) {
                $this->markNew();
            } else {
                $this->markDirty();
            }
        }
        return $this;
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        parent::__unset( $name );
        $this->markDirty();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->data['id'] = null;
        $this->markNew();
    }

    /**
     * Установить id
     * @param $id
     *
     * @return mixed
     * @throws Exception
     */
    public function setId( $id )
    {
        if ( ! isset( $this->data['id'] ) && is_numeric( $id ) ) {
            $this->data['id']   = $id;
            return;
        }
        throw new Exception('Attempting to set an existing object id');
    }

    /**
     * Вернет id
     * @return int|null
     */
    public function getId()
    {
        if ( isset( $this->data['id'] ) && $this->data['id'] ) {
            return $this->data['id'];
        }
        return null;
    }

    /**
     * Вернет модель данных
     * @param string $model
     * @return Model
     */
    public function getModel( $model = '' )
    {
        if ( '' === $model )
            return $this->model;
        else
            return Model::getModel($model);
    }

    /**
     * Сохранение
     * @return int
     */
    public function save()
    {
        return $this->model->save( $this );
    }

    /**
     * Удалить запись об объекте из базы
     */
    public function delete()
    {
        $this->model->delete( $this->getId() );
    }

    /**
     * Как новый
     * @return void
     */
    public function markNew()
    {
        Watcher::addNew( $this );
    }

    /**
     * Как удаленный
     * @return void
     */
    public function markDeleted()
    {
        Watcher::addDelete( $this );
    }

    /**
     * На обновление
     * @return void
     */
    public function markDirty()
    {
        Watcher::addDirty( $this );
    }

    /**
     * Стереть везде
     * @return void
     */
    public function markClean()
    {
        Watcher::addClean( $this );
    }

}