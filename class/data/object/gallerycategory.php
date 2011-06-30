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
    public function createAlias()
    {
        $alias_model    = $this->getModel('Alias');
        $structure_model= $this->getModel('Page');

        /**
         * @var Data_Object_Alias $alias
         */
        $alias      = $alias_model->createObject();

        $strpage    = $structure_model->find(
            array(
                'cond'      => ' controller = ? AND action = ? AND link = ? ',
                'params'    => array( 'gallery', 'index', $this->id ),
            )
        );

        if ( $strpage ) {
            return $strpage->alias . '/' . $alias->generateAlias( $this->name );
        } else {
            return $alias->generateAlias( $this->name );
        }
    }
}
