<?php
/**
 * Объект Новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

use Module\Page\Model\PageModel;

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
        if ( ! $this->data['alias'] || '0' == $this->data['alias']{0} ) {
            $this->alias = $this->name
                ? strtolower( $this->id . '-' . Sfcms::i18n()->translit( $this->name ) )
                : $this->id;
        }
        return $this->data['alias'];
    }

    public function onSetName()
    {
        $this->changed['alias'] = 'alias';
    }

    public function getTitle()
    {
        if ( isset( $this->data['title'] ) ) {
            return $this->data['title'];
        }
        return $this->data['name'];
    }

    public function getUrl()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');
        $page = $pageModel->findByControllerLink( 'news', $this->cat_id );
        if ( null !== $page ) {
            return  $page->alias . '/' . $this->getAlias();
        } else {
            return $this->getAlias();
        }
    }
}
