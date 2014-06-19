<?php
/**
 * Контроллер галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link    http://siteforever.ru
 * @link    http://ermin.ru
 */
namespace Module\Gallery\Controller;

use Sfcms;
use Sfcms\Controller;
use Module\Gallery\Object\Gallery;
use Module\Gallery\Object\Category;
use Module\Page\Object\Page;
use Module\Gallery\Model\GalleryModel;
use Module\Gallery\Model\CategoryModel;

class GalleryController extends Controller
{
    public function init()
    {
        if ($this->page){
            $this->tpl->getBreadcrumbs()->fromSerialize($this->page->get('path'));
        }
    }

    /**
     * Действие по-умолчанию
     * @return string|array
     */
    public function indexAction()
    {
        /**
         * @var Gallery $image
         * @var GalleryModel $model
         * @var CategoryModel $catModel
         */
//        $this->request->setTemplate( 'inner' );
        $model    = $this->getModel( 'Gallery' );
        $catModel = $this->getModel( 'GalleryCategory' );

        /*
         * Вывести изображение
         */
        if( $alias = $this->request->get( 'alias' ) ) {

            $image = $model->find( 'alias = ?', array( $alias ) );

            if( null === $image ) {
                return $this->t( 'Image not found' );
            }

            $crit = array(
                'cond'  => 'category_id = ? AND pos > ? AND deleted != 1',
                'params'=> array( $image->category_id, $image->pos ),
                'order' => 'pos ASC',
            );

            $next = $model->find( $crit );

            $crit[ 'cond' ]  = 'category_id = ? AND pos < ? AND deleted != 1';
            $crit[ 'order' ] = 'pos DESC';

            $pred = $model->find( $crit );

            /** @var $category Category */
            $category = $catModel->find( $image->category_id );

            $this->tpl->getBreadcrumbs()->addPiece($image->alias, $image->title);

            $this->request->setTitle($image->title);
            return $this->render('gallery.image', array(
                    'image' => $image,
                    'next'  => $next,
                    'pred'  => $pred,
                    'category' => $category,
                ));
        }

        $catId = $this->page->link;
        $category = null;
        if ($catId) {
            $category = $catModel->find($catId);
        }

        if ($category) {
            $crit = array(
                'cond'   => 'category_id = ? AND deleted != 1 AND hidden != 1',
                'params' => array($category->getId()),
            );

            $count = $model->count($crit['cond'], $crit['params']);

            $paging = $this->paging($count, $category->perpage, $this->page->alias);

            $crit['limit'] = $paging['limit'];
            $crit['order'] = 'pos';

            $rows = $model->findAll($crit);

            $this->tpl->assign(
                array(
                    'category' => $category,
                    'rows'     => $rows,
                    'page'     => $this->page,
                    'paging'   => $paging,
                )
            );

            return $this->tpl->fetch('gallery.category');
        }

        /**
         * Список категорий
         */
        $categories = null;

        $pageModel = $this->getModel('Page');
        if ($this->page) {
            $subPages  = $pageModel->findAll( array(
                 'condition' => ' parent = ? AND deleted != 1 ',
                 'params'    => array($this->page->getId()),
            ));

            /** @var Page $obj */
            $listSubpagesIds = array();
            foreach ($subPages as $obj) {
                if ($obj->get('link') && $obj->get('controller') == 'gallery') {
                    $listSubpagesIds[] = $obj->get('link');
                }
            }

            if (count($listSubpagesIds)) {
                $categories = $catModel->findAll(array(
                     'condition' => ' id IN ( ' . implode( ',', $listSubpagesIds ) . ' ) ',
                ));
            }
        }

//        $categories = $catModel->findAll();

        $this->tpl->assign( 'categories', $categories );
        return $this->tpl->fetch( 'gallery.categories' );
    }
}

