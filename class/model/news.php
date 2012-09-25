<?php
/**
 * Модель новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */


class Model_News extends Sfcms_Model
{
    private $form = null;

    /**
     * @var Model_NewsCategory
     */
    public $category;

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
     * @return form_Form
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_news_Edit();
        }
        return $this->form;
    }
}
