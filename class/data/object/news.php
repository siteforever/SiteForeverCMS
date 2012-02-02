<?php
/**
 * Объект Новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Object_News extends Data_Object
{
    public function getAlias()
    {
        $cat_id = $this->get('cat_id');
//        $category   = $this->getModel('NewsCategory')->find( $cat_id );

        $page   = $this->getModel('Page')->find(
            array(
                 'condition'    => 'link = ? AND controller = ?',
                 'params'       => array( $cat_id, 'news' ),
            )
        );

        if ( ! $page ) {
            return null;
        }

        $result = App::getInstance()->getRouter()->createLink(
            $page->get('alias'),
            array(
                 'doc'  => $this->getId(),
                 'title'=> Sfcms_i18n::getInstance()->translit( $this->get('name') ),
            )
        );

        return $result;
    }
}
