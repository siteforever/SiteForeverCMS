<?php
/**
 * Объект Изображения Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
require_once 'class/data/object.php';
class Data_Object_Gallery extends Data_Base_Gallery
{
    /**
     * @return string
     */
    public function getAlias()
    {
        /**
         * @var Model_Alias $model
         */
        $model  = $this->getModel('Alias');
        $result = $model->find(
            array(
                'cond'      => 'url = ?',
                'params'    => array( $this->createUrl() ),
            )
        );
        if ( $result &&  $result->alias) {
            return $result->alias;
        }

        $name   = $this->get('name');
        $name   = $name ? $name : $this->getId();
        //***********

        try {
            $alias  = $this->getCategory()->getAlias();
        } catch ( Data_Exception $e ) {
            return '';
        }

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

    /**
     * @return string
     */
    public function getAddr()
    {
        if ( $this->get('alias_id') ) {
            return $this->getAlias();
        } else {
            return trim( $this->createUrl(), '/' );
        }
    }
}
