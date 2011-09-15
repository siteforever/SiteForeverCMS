<?php
/**
 * Объект Категории Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Object_GalleryCategory extends Data_Object
{
    /**
     * Вернет псевдоним для категории
     * @return mixed|string
     */
    public function getAlias()
    {
        $alias_model    = $this->getModel('Alias');

        $strpage    = $this->getPage();

        if ( $strpage ) {
            return $strpage->alias;
        }
        else {
            return $alias_model->generateAlias( $this->name );
        }
    }

    /**
     * @return Data_Object_Page
     */
    public function getPage()
    {
        $model  = $this->getModel('Page');
//        $result = $model->find( array(
//            'cond'  => '`controller` = ? AND `action` = ? AND `link` = ? AND `deleted` = 0 ',
//            'params'=> array('gallery', 'index', $this->getId()),
//        ));
        $result = $model->find( array(
            'cond'  => '`action` = ? AND `link` = ? AND `deleted` = 0 ',
            'params'=> array('index', $this->getId()),
        ));
        if ( null === $result  )
            throw new Data_Exception(t('Page not found for gallery'));
        return $result;
    }
}
