<?php
/**
 * Объект Категории Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */

class Data_Object_GalleryCategory extends Data_Base_GalleryCategory
{
    /**
     * @var Data_Object_Page
     */
    private $_page = null;

    /**
     * Вернет псевдоним для категории
     * @return mixed|string
     */
    public function getAlias()
    {
        try {
            $strpage = $this->getPage();
        }
        catch ( Exception $e ) {
            return App::getInstance()->getRouter()->createServiceLink(
                'gallery', 'index', array( 'id'=> $this->getId() )
            );
        }

        if ( $strpage ) {
            return $strpage->get( 'alias' );
        }
        return '';
//        else {
//            return $alias_model->generateAlias( $this->get( 'name' ) );
//        }
    }

    /**
     * Вернет страницу, к которой привязана категория
     * @return Data_Object_Page
     */
    public function getPage()
    {
        if (null === $this->_page) {
            $model = $this->getModel( 'Page' );

            $this->_page = $model->find(
                array(
                    'cond'  => 'action = ? AND controller = ? AND link = ? AND deleted = 0 ',
                    'params'=> array( 'index', 'gallery', $this->getId() ),
                )
            );
            if (null === $this->_page) {
                throw new Data_Exception( t( 'Page not found for gallery' ) );
            }
        }
        return $this->_page;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        //if ( ! isset( $this->data['image'] ) || ! $this->data['image'] ) {
            $imageModel = $this->getModel('Gallery');
            $image = $imageModel->find(array(
                'cond' => 'category_id = ? AND hidden != ?',
                'params' => array($this->id, 1),
                'order' => 'pos',
            ));
            if ( $image ) {
                $this->data['image'] = $image->image;
                $this->save();
            } else {
                $this->data['image'] = '';
            }
        //}
        return $this->data['image'];
    }

}
