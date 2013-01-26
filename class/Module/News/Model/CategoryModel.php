<?php
/**
 * Категории новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Model;

use Sfcms\Model;
use Forms_News_Category;

class CategoryModel extends Model
{
    /** @var Forms_News_Category */
    private $form = null;

    public function relation()
    {
        return array(
            'Page' => array( self::BELONGS, 'Page', 'link' ),
        );
    }

    /**
     * @return Forms_News_Category
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_News_Category();
        }
        return $this->form;
    }

}
