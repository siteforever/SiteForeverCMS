<?php
/**
 * Объект Изображения Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
require_once 'class/data/object.php';

/**
 * @property string title
 * @property string h1
 * @property \Data_Object_GalleryCategory Category
 */
class Data_Object_Gallery extends Data_Base_Gallery
{
    /**
     * @return string
     */
    public function getAlias()
    {
        $alias = $this->get('name') ? Sfcms_i18n::getInstance()->translit( $this->get('name') ) : $this->getId();
        if ( ! $this->data['alias'] || $this->data['alias'] != $alias ) {
            $this->data['alias'] = $alias;
            $this->markDirty();
        }
        return $this->data['alias'];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        /** @var $pageModel Model_Page */
        $pageModel = $this->getModel('Page');
        $page = $pageModel->findByControllerLink( 'gallery', $this->category_id );
        if ( null !== $page ) {
            return $page->alias . '/' . $this->alias;
        } else {
            return $this->alias;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = '';
        if ( $this->name ) {
            $title = $this->name;
        }
        return $title;
    }
}
