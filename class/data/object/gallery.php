<?php
/**
 * Объект Изображения Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
require_once 'class/data/object.php';
class Data_Object_Gallery extends Data_Object
{
    /**
     * @return string
     */
    public function getAlias()
    {
        $name   = $this->get('name');
        $name   = $name ? $name : $this->getId();

        $alias  = $this->getCategory()->getAlias();

        $alias  .= '/'.$this->getModel('Alias')->generateAlias( $name );

        return $alias;
    }

    /**
     * @return string
     */
    public function createUrl()
    {
        return App::getInstance()->getRouter()->createServiceLink('gallery','index',array('img'=>$this->getId()));
    }

    /**
     * @return Data_Object_GalleryCategory
     */
    public function getCategory()
    {
        $model  = $this->getModel('GalleryCategory');

        $result = $model->find( $this->get('category_id') );

        if ( null === $result )
            throw new Data_Exception(t('Category not found'));
        return $result;
    }

    public function getAddr()
    {
        if ( $this->get('alias_id') ) {
            return $this->getAlias();
        } else {
            return trim( $this->createUrl(), '/' );
        }
    }
}
