<?php
/**
 * Модель новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Model;

use Db_Criteria;
use Sfcms_Model;
use Form_Form;
use Forms_News_Edit;
use Data_Collection;
use Data_Object_Page;
use Data_Object_News;

class NewsModel extends Sfcms_Model
{
    private $form = null;

    /**
     * @var CategoryModel
     */
    public $category;

    public function tableClass()
    {
        return 'Data_Table_News';
    }

    public function objectClass()
    {
        return 'Data_Object_News';
    }

    public function Init()
    {
        $this->category = self::getModel( 'NewsCategory' );
    }

    public function relation()
    {
        return array(
            'Category' => array( self::BELONGS, 'NewsCategory', 'cat_id' ),
        );
    }

    /**
     * Поиск объекта по алиасу
     * @param $alias
     * @return Data_Object_News
     */
    public function findByAlias( $alias )
    {
        $criteria = $this->createCriteria();
        $criteria->condition = 'alias = ? AND deleted = 0';
        $criteria->params    = array($alias);
        $obj = $this->find($criteria);
        return $obj;
    }

    /**
     * @param array|Db_Criteria $crit
     * @return array|Data_Collection
     */
    public function findAllWithLinks($crit = array())
    {
        $data_all   = $this->findAll( $crit );

        $list_id    = array();
        foreach ( $data_all as $news ) {
            /** @var Data_Object_News $news */
            $list_id[]  = $news->cat_id;
        }

        $structure  = self::getModel('Page');

        //printVar( Data_Watcher::instance()->dumpDirty() );
        $page_data_all = $structure->findAll(array(
            'select'  => 'id, link, alias',
            'cond'    => "deleted = 0 AND alias != 'index' AND controller = 'news' "
                            . ( count( $list_id )
                                ? " AND link IN (".join(',', $list_id).")"
                                : "" )
        ));

        foreach( $data_all as $news ) {
            /**
             * @var Data_Object_Page $page
             */
            foreach ( $page_data_all as $page ) {
                if ( $news['cat_id'] == $page['link'] ) {
                    $news->alias   = $page->alias;
                    $news->markClean();
                    break;
                }
            }
        }

        return $data_all;
    }

    /**
     * @return Form_Form
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_News_Edit();
        }
        return $this->form;
    }
}
