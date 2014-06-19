<?php
namespace Module\Gallery\Model;

use Module\Gallery\Object\Category;
use Module\Page\Object\Page;
use Sfcms\Model;
use Module\Gallery\Form\CategoryForm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryModel extends Model implements EventSubscriberInterface
{
    protected $form;

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
            'category.save.success' => array('onSaveSuccess', 0),
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
    public function pluginPageSaveStart(Model\ModelEvent $event)
    {
        /** @var Page $obj */
        $obj = $event->getObject();
        $category = $obj->link ? $this->find($obj->link) : $this->createObject();
        /** @var $category Category */
        $category->name         = $obj->name;
        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted;

        $category->save();
        $obj->link = $category->id;
        $obj->image = $category->getImage();
    }

    public function onSaveSuccess(Model\ModelEvent $event)
    {
        $obj = $event->getObject();
        if ($obj instanceof Category) {
            $image = $obj->getImage();
            $pageObj = $this->getModel('Page')->find('link = ? and controller = ?', [$obj->id, 'gallery']);
            if ($pageObj && $pageObj->image != $image) {
                $pageObj->image = $image;
                $this->getModel('Page')->save($pageObj);
            }
        }
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
