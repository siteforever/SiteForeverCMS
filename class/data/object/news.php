<?php
/**
 * Объект Новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 *
 * @property $deleted
 */

/**
 * @property $id
 * @property $cat_id
 * @property $author_id
 * @property $alias
 * @property $name
 * @property $notice
 * @property $text
 * @property $date
 * @property $title
 * @property $keywords
 * @property $description
 * @property $hidden
 * @property $protected
 * @property $deleted
 */
class Data_Object_News extends Data_Object
{
    public function getAlias()
    {
        $alias = $this->name ? Sfcms_i18n::getInstance()->translit( $this->get('name') ) : $this->id;
        if ( ! $this->data['alias'] || $this->data['alias'] != $alias ) {
            $this->data['alias'] = $alias;
            $this->markDirty();
        }
        return $this->data['alias'];
    }

    public function getUrl()
    {
        /** @var $pageModel Model_Page */
        $pageModel = $this->getModel('Page');
        $page = $pageModel->findByControllerLink( 'news', $this->cat_id );
        if ( null !== $page ) {
            return  $page->alias . '/' . $this->alias;
        } else {
            return $this->alias;
        }
    }
}
