<?php
/**
 * Событие модели
 * @author: keltanas
 */

namespace Sfcms\Model;

use Sfcms\Data\Object as DataObject;
use Sfcms\Model;
use Symfony\Component\EventDispatcher\Event;

class ModelEvent extends Event
{
    /** @var DataObject */
    protected $object;

    /** @var Model */
    protected $model;

    /** @var bool */
    protected $continue = true;

    public function __construct( DataObject $object, Model $model )
    {
        $this->object = $object;
        $this->model = $model;
    }

    /**
     * @param boolean $continue
     */
    public function setContinue( $continue )
    {
        $this->continue = $continue;
    }

    /**
     * Продолжать ли работу дальше?
     *
     * Например, если событие model.save.start будет содержать continue = false, то объект не будет сохранен
     *
     * @return boolean
     */
    public function getContinue()
    {
        return $this->continue;
    }

    /**
     * @param \Sfcms\Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }

    /**
     * @return \Sfcms\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param DataObject $object
     */
    public function setObject( $object )
    {
        $this->object = $object;
    }

    /**
     * @return DataObject
     */
    public function getObject()
    {
        return $this->object;
    }


}
