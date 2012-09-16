<?php
/**
 * Интервейс контейнера для данных
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

abstract class Data_Object extends \Sfcms\Component
{
    /**
     * @var Sfcms_Model
     */
    protected $model  = null;

    /**
     * @var Data_Table
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
     * @param Sfcms_Model $model
     * @param array $data
     */
    public function __construct( Sfcms_Model $model, $data )
    {
        $this->model    = $model;
        $this->table    = $model->getTable();
        $this->relation = $this->model->relation();

        $this->setAttributes( $data );
        $this->new = false;

        // @TODO Пока не будем помечать новые объекты для добавления
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
     * @return array|Data_Object|mixed|null
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
     * @return \Data_Object|\Sfcms\Component
     */
    public function set( $key, $value )
    {
        $oldValue = isset( $this->data[$key] ) ? $this->data[$key] : null;
        parent::set( $key, $value );
        if ( null === $oldValue || $oldValue != $value ) {
            if ( ! $this->new ) {
                $this->changed[ $key ] = $key;
            }
            $this->markDirty();
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
     * @return Sfcms_Model
     */
    public function getModel( $model = '' )
    {
        if ( '' === $model )
            return $this->model;
        else
            return Sfcms_Model::getModel($model);
    }

    public function getTable()
    {
        return $this->model->getTable();
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
     * Как новый
     * @return void
     */
    public function markNew()
    {
        Data_Watcher::addNew( $this );
    }

    /**
     * Как удаленный
     * @return void
     */
    public function markDeleted()
    {
        Data_Watcher::addDelete( $this );
    }

    /**
     * На обновление
     * @return void
     */
    public function markDirty()
    {
        Data_Watcher::addDirty( $this );
    }

    /**
     * Стереть везде
     * @return void
     */
    public function markClean()
    {
        Data_Watcher::addClean( $this );
    }

}
