<?php
/**
 * Категории новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Model;

use Module\News\Object\Category;
use Sfcms\Model;
use Module\News\Form\CategoryForm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryModel extends Model implements EventSubscriberInterface
{
    /** @var CategoryForm */
    private $form = null;

    public function relation()
    {
        return array(
            'Page' => array( self::BELONGS, 'Page', 'link' ),
        );
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
            'plugin.page-news.save.start' => array('pluginPageSaveStart', 0),
        );
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами новостей
     *
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function pluginPageSaveStart(Model\ModelEvent $event)
    {
        $obj = $event->getObject();

        $category = null;
        if ( $obj->link ) {
            $category = $this->find( $obj->link );
        }
        if ( ! $category ) {
            $category = $this->createObject();
        }

        /** @var $category Category */
        $category->name         = $obj->name;

        if ( ! $category->id ) {
            $category->description  = '';
            $category->show_list    = 1;
            $category->type_list    = 1;
            $category->show_content = 0;
            $category->per_page     = 10;
        }

        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted ?: 0;

        $category->save();

        $obj->link = $category->id;
    }

    /**
     * @return CategoryForm
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new CategoryForm();
        }
        return $this->form;
    }

}
