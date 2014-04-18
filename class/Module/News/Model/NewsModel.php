<?php
/**
 * Модель новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Model;

use Module\News\Object\News;
use Module\Page\Object\Page;
use Sfcms\Data\Collection;
use Sfcms\Db\Criteria;
use Sfcms\Model;
use Sfcms\Form\Form;
use Module\News\Form\NewsForm;

class NewsModel extends Model
{
    private $form = null;

    public function relation()
    {
        return array(
            'Category' => array( self::BELONGS, 'Category', 'cat_id' ),
        );
    }

    public function onSaveSuccess(Model\ModelEvent $e)
    {
        $obj = $e->getObject();
        if (empty($obj->alias) || '0' == $obj->alias{0}) {
            $obj->alias = $obj->name
                ? strtolower($obj->id . '-' . \Sfcms::i18n()->translit($obj->name))
                : $obj->id;
            $obj->markDirty();
            $obj->save();
        }
    }

    /**
     * Вернет список главных новостей
     * @param int $limit
     *
     * @return array|Collection
     */
    public function findAllMainNews( $limit = 5 )
    {
        return $this->findAll(
            'deleted = 0 AND hidden = 0 AND protected = 0 AND main = 1',
            array(), '`date` DESC, `priority` DESC, `id` DESC',$limit);
    }

    /**
     * Поиск объекта по алиасу
     * @param $alias
     * @return News
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
     * @param array|Criteria $crit
     * @return array|Collection
     */
    public function findAllWithLinks($crit = array())
    {
        $data_all   = $this->findAll( $crit );

        $list_id    = array();
        foreach ( $data_all as $news ) {
            /** @var News $news */
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
             * @var Page $page
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
     * @return Form
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new NewsForm();
        }
        return $this->form;
    }
}
