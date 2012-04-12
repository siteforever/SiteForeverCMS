<?php
/**
 * Объект Категории Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */

class Data_Object_GalleryCategory extends Data_Object
{
    /**
     * @var Data_Object_Page
     */
    private $_page = null;

    /**
     * @var string
     */
    private $_image = null;

    /**
     * Вернет псевдоним для категории
     * @return mixed|string
     */
    public function getAlias()
    {
        /**
         * @var Model_Alias $alias_model
         */
        $alias_model = $this->getModel( 'Alias' );

        try {
            $strpage = $this->getPage();
        }
        catch ( Exception $e ) {
            return App::getInstance()->getRouter()->createServiceLink(
                'gallery', 'index', array( 'id'=> $this->getId() )
            );
        }

        if ($strpage) {
            return $strpage->get( 'alias' );
        }
        else {
            return $alias_model->generateAlias( $this->get( 'name' ) );
        }
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
     * Вернет изображение категории
     * @return string
     */
    public function getImage()
    {
        if ( $this->data['thumb'] ) {
            return $this->data['thumb'];
        }
        if (null === $this->_image) {
            $this->_image = '';
            $model        = $this->getModel( 'Gallery' );
            $crit         = array(
                'cond'      => 'category_id = ?',
                'params'    => array( $this->getId() ),
                'order'     => 'pos',
                'limit'     => 1,
            );
            $image        = $model->find( $crit );
            if ($image) {
                $this->_image = $image->get( 'thumb' );
                $this->data['thumb'] = $this->_image;
                $this->markDirty();
            }
        }
        return $this->_image;
    }
}
