<?php
namespace Module\Gallery\Model;

use Module\Gallery\Object\Category;
use Module\Gallery\Object\Gallery;
use Sfcms\Model;
use Module\Gallery\Form\CategoryForm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryModel extends Model implements EventSubscriberInterface
{
    protected $form;

    /**
     * @return Gallery
     */
    function gallery()
    {
        return self::getModel('Gallery');
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'plugin.page-gallery.save.start' => array('pluginPageSaveStart', 0),
        );
    }

    public function relation()
    {
        return array(
            'Images' => array( self::HAS_MANY, 'Gallery', 'category_id' ),
            'Page'   => array( self::HAS_ONE, 'Page', 'link' ),
        );
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами галереи
     *
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function pluginPageSaveStart( Model\ModelEvent $event )
    {
        $obj = $event->getObject();
        if ( $obj->link ) {
            $category = $this->find( $obj->link );
        } else {
            $category = $this->createObject();
        }
        /** @var $category Category */
        $category->name         = $obj->name;
        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted;

        $category->save();
        $obj->link = $category->id;
    }

    /**
     * Удаление категории
     * @param int $id
     * @return mixed
     */
    public function remove( $id )
    {
        /** @var Gallery $gallery */
        $category   = $this->find( $id );
        if ( $category ) {
            $images = $this->gallery()->findAll(array(
                'cond'      => 'category_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            ));
            foreach ( $images as $img ) {
                $this->gallery()->delete( $img['id'] );
            }
            $dir = ROOT . $this->config->get('gallery.dir')
                 . DIRECTORY_SEPARATOR.substr( '0000' . $category['id'], -4, 4 );
            @rmdir( $dir );
            $category->markDeleted();
        }

        return;
    }

    /**
     * @return CategoryForm
     */
    function getForm()
    {
        if (is_null($this->form)) {
            $this->form = new CategoryForm();
        }
        return $this->form;
    }
}
