<?php
/**
 * Контроллер каталога
 * @author KelTanas
 */
namespace Module\Catalog\Controller;

use App;
use Module\Catalog\Form\CatalogForm;
use Module\Catalog\Form\CommentForm;
use Module\Catalog\Model\PropertyModel;
use Module\Catalog\Object\Comment;
use Sfcms\Controller;
use Module\Catalog\Model\CatalogModel;
use Module\Catalog\Object\Catalog;
use Module\Catalog\Object\Property;
use Module\Page\Model\PageModel;
use Sfcms\Model\Exception;
use Sfcms_Http_Exception;
use Sfcms\Request;
use Sfcms\Form\Form;
use Sfcms\Form\FormFieldAbstract;
use Sfcms;
use Sfcms_Filter;
use Sfcms_Filter_Group;
use Sfcms_Filter_Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogController extends Controller
{
    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN => array(
                'admin', 'delete', 'save', 'hidden', 'price', 'move', 'saveorder', 'category', 'trade', 'goods'
            ),
        );
    }

    public function init()
    {
        if ($this->page){
            $this->request->setTitle($this->page->title);
            $this->tpl->getBreadcrumbs()->fromSerialize($this->page->get('path'));
        }
    }


    /**
     * Действие по умолчанию
     * @param string $alias
     * @return mixed
     * @throws Sfcms_Http_Exception
     */
    public function indexAction($alias)
    {
        /**
         * @var Catalog $item
         * @var CatalogModel $catalogModel
         * @var PageModel $pageModel
         */
        $catId = $this->page->link;

        $catalogModel = $this->getModel('Catalog');

        /** @var $item Catalog */
        $item = null;
        if ($alias) {
            $item = $catalogModel->find('`alias` = ?', array($alias));
        } elseif ($catId) {
            $item = $catalogModel->find($catId);
        }

        if (null === $item) {
            throw new HttpException(404, $this->t('Catalogue part not found with id ') . $catId);
        }

        if ($item->cat && $this->page) {
            if ($this->page->link != $item->id) {
                throw new NotFoundHttpException(sprintf(
                    'Page [link:%d] not corresponds with item [id:%d]', $this->page->link, $item->id
                ));
            }
        } else {
            $this->getTpl()->getBreadcrumbs()->addPiece(null, $item->name);
        }
        $this->request->setTitle($item->title);
        $this->tpl->assign('page_number', $this->request->get('page', 1));

        return $item->cat ? $this->viewCategory($item) : $this->viewProduct($item);
    }

    /**
     * Вернет Cat_id запроса
     * @param int $id
     * @param int $cat
     * @return int
     */
    protected function getCatId($id, $cat)
    {
        return $id ?: $cat;
    }

    /**
     * Открывается категория
     * @param Catalog $item
     * @return Response
     */
    protected function viewCategory(Catalog $item)
    {
        $config = $this->container->getParameter('catalog');

        $level = $config['level'];
        $pageNum = $this->request->query->getDigits('page', 1);

        $manufacturerId = $this->request->get('filter[manufacturer]', false);
        $materialId     = $this->request->get('filter[material]', false);

        $order = $config['order_default'];
        $orderList = $config['order_list'];
        // Примеряем способ сортировки к списку из конфига
        if ($orderList && is_array($orderList)) {
            if (!($set = $this->request->get('order'))) {
                $set = $this->request->getSession()->get('Sort', false);
            }
            if ($set && isset($config['order_list'][$set])) {
                $order = $set;
                $this->request->set('order', $order);
                $this->request->getSession()->set('Sort', $order);
                $this->request->setTitle(sprintf('%s / %s "%s"', $this->request->getTitle(), $this->t('sorting'), $this->t($config['order_list'][$set])));
            }
        }

        $templateConfig = $this->container->getParameter('template');
        $this->getTpl()->caching(isset($templateConfig['caching']) && $templateConfig['caching']);
        $cacheKey = sprintf('catalog%d%d%d%d%s', $item->id, $pageNum, $manufacturerId, $materialId, $order);
        if ($this->getTpl()->isCached('catalog.view_category', $cacheKey)) {
            $response = $response = $this->render('catalog.view_category', [], $cacheKey);
        } else {
            /** @var $catModel CatalogModel */
            $catModel = $this->getModel('Catalog');
            $parent   = $catModel->find($item->getId());

            $categoriesId = array($item->getId());
            if ($level != 1) {
                $categoriesId = array_merge(
                    $categoriesId,
                    $catModel->getAllChildrensIds($item->getId(), $level - 1)
                );
            }

            $criteria = $catModel->createCriteria();

            $criteria->condition = " `deleted` = 0 AND `hidden` = 0 AND `cat` = 0 ";
            if (count($categoriesId)) {
                $criteria->condition .= ' AND `parent` IN (?) ';
                $criteria->params[] = $categoriesId;
            }
            if ($manufacturerId) {
                $criteria->condition .= ' AND `manufacturer` IN (?) ';
                $criteria->params[] = $manufacturerId;
            }
            if ($materialId) {
                $criteria->condition .= ' AND `material` IN (?) ';
                $criteria->params[] = $materialId;
            }
//            . ( count( $categoriesId ) ? ' AND `parent` IN ('.implode(',',$categoriesId ) . ')' : '' )
//            . ( $manufId ? ' AND `manufacturer` = '.$manufId.' ' : '' );

            // количество товаров
            $count = $catModel->count($criteria);
            if ($order) {
                $criteria->order = str_replace(array('-d', '_d'), ' DESC', strtolower($order));
            }

            $this->get('logger')->debug('Parent', $parent->attributes);

            $paging = $this->paging(
                $count,
                $config['onPage'],
                $this->router->generate(trim($parent->url, '/')),
                $cacheKey
            );

            if ($paging->page > 1) {
                $this->request->setTitle(sprintf('%s / %s %d', $this->request->getTitle(), $this->t('page'), $paging->page));
            }

            $criteria->limit = $paging->limit;

            $list = $catModel->with('Gallery', 'Manufacturer', 'Material', 'Properties')->findAll($criteria);

            // Оптимизированный список свойств
            $properties = array();
            /** @var Catalog $catItem */
            foreach ($list as $catItem) {
                for ($i = 0; $i <= 9; $i++) {
                    $properties[$catItem->getId()][$parent['p' . $i]] = $catItem['p' . $i];
                }
            }

            $cats = $catModel->findAll( array(
                    'cond'      => 'deleted = 0 AND hidden = 0 AND cat = 1 AND parent = ?',
                    'params'    => array($item->getId()),
                    'order'     => 'pos DESC',
                )
            );

            $response = $this->render('catalog.viewcategory', array(
                'parent'    => $parent,
                'properties'=> $properties,
                'category'  => $item,
                'list'      => $list,
                'cats'      => $cats,
                'paging'    => $paging,
                'user'      => $this->auth->currentUser(),
                'order_list'=> $config['order_list'],
                'order_val' => $this->request->get('order'),
            ), $cacheKey);
        }
        $this->getTpl()->caching(false);

        return $response;
    }

    /**
     * Открывается товар
     *
     * @param Catalog $item
     *
     * @return string
     */
    protected function viewProduct(Catalog $item)
    {
        $catalog_model = $this->getModel('Catalog');

        $properties = array();

        if ($item->parent) {
            $properties = $this->buildParamView($catalog_model->find($item['parent']), $item);
        }

        $gallery_model = $this->getModel('CatalogGallery');

        $gallery = $gallery_model->findAll(
            array(
                'cond'   => ' cat_id = ? AND hidden = 0 ',
                'params' => array($item->id),
            )
        );

        $response = $this->render('catalog.viewproduct', array(
            'item'       => $item,
            'inBasket'   => $this->getBasket()->getCount($item->id),
            'parent'     => $item->parent ? $catalog_model->find($item->parent) : null,
            'properties' => $properties,
            'gallery'    => $gallery,
            'user'       => $this->auth->currentUser(),
        ));

        $this->request->setTitle($item->name);

        return $response;
    }

    /**
     * Удалит раздел или товар
     */
    public function deleteAction()
    {
        /** @var Catalog $item */
        $id = $this->request->get( 'id' );
        /** @var CatalogModel $catalog */
        $catalog = $this->getModel( 'Catalog' );
        $item = $catalog->find( $id );
        if( $item ) {
            $catalog->remove( $id );
        }
        return array('error'=>0,'msg'=>'');
    }


    /**
     * Создать список параметров
     * @param Catalog $cat
     * @param Catalog $item
     *
     * @return array
     */
    public function buildParamView( Catalog $cat, Catalog $item )
    {
        $properties = array( $item->getId() => array() );

        for( $p = 0; $p < 10; $p ++ )
        {
            if( $cat[ 'p' . $p ] == '' ) {
                continue;
            }

            $item[ 'p' . $p ] = trim( $item[ 'p' . $p ], '; ' );

            if( strpos( $item[ 'p' . $p ], ';' ) !== false ) {
                $par_list = explode( ';', $item[ 'p' . $p ] );
                $html     = array( "<select name='p[{$cat['p'.$p]}]'>" );
                foreach( $par_list as $par_key => $par_val ) {
                    $html[ ] = "<option value='{$par_val}'>{$par_val}</option>";
                }
                $html[ ]          = "</select>";
                $item[ 'p' . $p ] = join( "\n", $html );
            }
            elseif( $item[ 'p' . $p ] != '' ) {
                $val              = $item[ 'p' . $p ];
                $item[ 'p' . $p ] = $val . "<input type='hidden' name='p[{$cat['p'.$p]}]' value='{$val}' />";
            }
            else {
                continue;
            }

            $properties[ $item->getId() ][ $cat[ 'p' . $p ] ] = $item[ 'p' . $p ];

            $item->markClean();
        }
        return $properties;
    }

    /**
     * Сохранение формы
     * @return mixed
     */
    public function saveAction()
    {
        /**
         * @var CatalogModel $catalogModel
         * @var FormFieldAbstract $field
         * @var Form $form
         * @var Catalog $catalogEntry
         */
        $catalogModel = $this->getModel('Catalog');
        /** @var CatalogForm $form */
        $form = $this->get('catalog.product.form');

        // Если форма отправлена
        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                /** @var $object Catalog */
                $catalogEntry = $form->getChild('id')->getValue()
                    ? $catalogModel->find($form->getChild('id')->getValue())
                    : $catalogModel->createObject();

                $data = $form->getData();
                $catalogEntry->setAttributes($data);
                if ($catalogEntry->isStateCreate()) {
                    $catalogEntry->save();
                }

                // Сохранение внешних полей
                if ( "1" != $catalogEntry->cat && $catalogEntry->type_id ) {
                    /** @var PropertyModel $propModel */
                    $propModel = $this->getModel('ProductProperty');
                    $entryType = $catalogEntry->Type;
                    $fields = $entryType->Fields;
                    $postField = $this->request->request->get('field');
                    foreach ($fields as $entryField) {
                        /** @var $property Property */
                        $property = $propModel->findProductPropertyByField($catalogEntry, $entryField);
                        if (!$property) {
                            $property = $propModel->createObject();
                            $property->product_id       = $catalogEntry->id;
                            $property->product_field_id = $entryField->id;
                            $property->markNew();
                        }
                        $property->pos = $entryField->pos;
                        $property->set('value_' . $entryField->type,  $postField[$entryField->id]);
                    }
                }

                if ($catalogEntry->getId() && $catalogEntry->getId() == $catalogEntry->parent) {
                    // раздел не может быть замкнут на себя
                    return array('error' => 1, $this->t('The section can not be in myself'));
                }
                return $this->renderJson(array('error' => 0, 'msg' => $this->t('Data save successfully')));
            } else {
                return $this->renderJson(
                    array('error' => 1, 'msg' => $form->getFeedbackString(), 'errors' => $form->getErrors())
                );
            }
        }
        return $this->renderJson(array('error'=>1));
    }

    /**
     * Генерит хлебные крошки для админки каталога
     * @param string $path serrialized array [ item{id}, item{id}, item{id} ]
     *
     * @return string
     */
    public function adminBreadcrumbs( $path )
    {
        $bc = array(
            Sfcms::html()->link($this->t('catalog','Catalog'),'catalog/admin')
        ); // breadcrumbs
        if( $arrPath = @unserialize( $path ) ) {
            if( $arrPath && is_array( $arrPath ) ) {
                foreach( $arrPath as $val ) {
                    $bc[ ] = Sfcms::html()->link($val['name'],'catalog/admin',array('part'=>$val['id']))
                           . Sfcms::html()->link(
                                Sfcms::html()->icon('pencil', $this->t('Edit')),
                                'catalog/category',
                                array('edit'=>$val['id']),
                                'edit'
                            );
                }
            }
        }
        return '<ul class="breadcrumb"><li>'.$this->t('catalog','Path').': ' . join( '</li><li>', $bc ) . '</li></ul>';
    }

    /**
     * Построит крошки для админки исходя из $id раздела или товара
     * @param $id
     *
     * @return string
     */
    public function adminBreadcrumbsById( $id )
    {
        /** @var Catalog $item */
        $item = $this->getModel( 'Catalog' )->find( $id );
        if ( $item ) {
            return $this->adminBreadcrumbs( $item->path() );
        }
        return null;
    }


    /**
     * Действие панели администратора
     * @return mixed
     */
    public function adminAction()
    {
        /**
         * @var CatalogModel $catalogFinder
         * @var Catalog $parent
         */
        $catalogFinder = $this->getModel( 'Catalog' );

        $filter = trim( $this->request->get( 'goods_filter' ) );
        if( $filter ) {
            $filter = preg_replace( '/[^\d\wа-яА-Я]+/u', '%', $filter );
            $filter = str_replace( array( '%34', '&#34;' ), '', $filter );
            $filter = preg_replace( '/[ %]+/u', '%', $filter );
            $filter = trim( $filter, '%' );
        }

        if( $this->request->get( 'delete' ) == 'group' ) {
            $this->groupAjaxDelete();
            return '';
        }

        $part = $this->request->get( 'part' );
        $part = $part ? $part : '0';

        try {
            if ( ! $part ) {
                throw new Exception();
            }
            $parent = $catalogFinder->find( $part );
        } catch( Exception $e ) {
            $parent = $catalogFinder->createObject(
                array(
                    'id'    => 0,
                    'parent'=> 0,
                    'path'  => '',//serialize(array()),
                )
            );
            $parent->markClean();
        }

        // Если смотрим список в товаре, то переместить на редактирование
        if( $parent->getId() && ! $parent->cat ) {
            return $this->redirect( '', array( 'edit'=> $parent->getId() ) );
        }

        $crit = array();
        if( ! $filter ) {
            $crit[ 'cond' ]   = 'deleted = 0 AND parent = :parent';
            $crit[ 'params' ] = array( ':parent'=> $part );
        } else {
            $crit[ 'cond' ]   = 'deleted = 0 AND cat = 0 AND ( id LIKE :filter OR articul LIKE :filter OR name LIKE :filter )';
            $crit[ 'params' ] = array( ':filter'=> '%' . $filter . '%' );
        }

        $count  = $catalogFinder->count( $crit[ 'cond' ], $crit[ 'params' ] );
        $paging = $this->paging( $count, 10, $this->router->createServiceLink('catalog','admin',array('part'=>$part)) );

        $crit[ 'limit' ] = $paging->limit;
        $crit[ 'order' ] = 'pos';

        $list = $catalogFinder->findAll( $crit );

        if ( $parent->path ) {
            $breadcrumbs    = $this->adminBreadcrumbs( $parent->path );
        } else {
            $breadcrumbs    = $this->adminBreadcrumbsById( $parent->id );
        }

        $this->request->setTitle( 'Каталог' );
        return array(
            'filter'         => trim( $this->request->get( 'goods_filter' ) ),
            'parent'         => $parent,
            'id'             => $part,
            'part'           => $part,
            'breadcrumbs'    => $breadcrumbs,
            'list'           => $list,
            'paging'         => $paging,
            'moving_list'    => $catalogFinder->getCategoryList(),
        );
    }

    /**
     * Правка товара
     *
     * @param int $edit Товар, который править
     * @param int $add Раздел, куда добавлять
     * @param int $type Тип товара
     *
     * @return mixed
     */
    public function tradeAction($edit, $add, $type)
    {
        /**
         * @var CatalogModel $catalogModel
         * @var Catalog $pitem
         * @var FormFieldAbstract $field
         * @var Sfcms_Filter_Collection $filter
         * @var Sfcms_Filter $fvalues
         */

        $catalogModel = $this->getModel('Catalog');

        if ($add) {
            $parentId = $add;
        } else {
            $parentId = $this->request->getSession()->get('catalogCategory', 0);
        }

        /** @var CatalogForm $form */
        $form = $this->get('catalog.product.form');

        if ($type) {
            $form->type_id = $type;
        }

        /** @var $item Catalog */
        if ($edit) { // если раздел существует
            $item     = $catalogModel->find($edit);
            $parentId = $item['parent'];
            $form->setData($item->getAttributes());
        } else {
            $item = $catalogModel->find('`name` IS NULL AND `deleted` = 1');
            if (null === $item) {
                $item = $catalogModel->createObject();
                $item->deleted = 1;
                $item->save();
            }
            $form->id       = $item->id;
            $form->parent   = $parentId;
            $form->cat      = 0;
            $form->deleted  = 0;
        }

        $this->request->setTitle($item->title);

        // ЕСЛИ ТОВАР
        //$form->image->show();
//        $form->getField( 'icon' )->hide();
        $form->getChild( 'articul' )->show();
        $form->getChild( 'material' )->show();
        $form->getChild( 'manufacturer' )->show();
        $form->getChild( 'price1' )->show();
        $form->getChild( 'price2' )->show();
        $form->getChild( 'sort_view' )->hide();

        //$form->top->show();
        $form->getChild( 'byorder' )->show();
        $form->getChild( 'absent' )->show();

        if ($form->sale_start <= 0 || $form->sale_stop <= 0) {
            $form->sale_start = $form->sale_stop = time();
        }

        // показываем поля родителя
        if ($parentId) {
            $parent = $catalogModel->find($parentId);
        } else {
            $parent = null;
        }

        if ($parent) {
            $form->applyFilter($parentId);
            $form->applyProperties($parent->attributes, isset($fvalues) ? $fvalues : null);
        } else {
            for ($i = 0; $i < 10; $i++) {
                $form->getChild('p' . $i)->hide();
            }
        }

        if ($edit) {
            $catgallery    = new GalleryController($this->request);
            $catgallery->setContainer($this->container);
            $gallery_panel = $catgallery->getPanel($edit);
            $this->tpl->assign('gallery_panel', $gallery_panel);
        }

        if (!$item->type_id && $type) {
            $item->type_id = $type;
        }
        // Обработка полей из модуля полей
        if ( $item->Type ) {
            $fields = $item->Type->Fields;
        }

        $this->request->setTitle($this->t('Catalog'));

        return array(
            //            'breadcrumbs' => $this->adminBreadcrumbsById( $parentId ),
            'item'   => $item,
            'form'   => $form,
            'fields' => isset($fields) ? $fields : array(),
            'cat'    => $form->id,
        );
    }

    /**
     * Правка категории
     * @param int $edit
     * @param int $add
     * @return array
     */
    public function categoryAction($edit, $add)
    {
        /**
         * @var CatalogModel $catalog
         * @var FormFieldAbstract $field
         * @var Form $form
         */
        $catalog = $this->getModel( 'Catalog' );

        $id        = $edit;
        $parent_id = $add ?: 0;

        $form = $this->get('catalog.product.form');

        if( $id ) { // если редактировать
            $item      = $catalog->find( $id );
            $parent_id = isset( $item[ 'parent' ] ) ? $item[ 'parent' ] : 0;
            $form->setData( $item->getAttributes() );
        } else { // если новый
            $item = $catalog->createObject();
            $form->getChild( 'parent' )->setValue( $parent_id );
            $form->getChild( 'cat' )->setValue( 1 );
        }

        // наследуем поля родителя
        $parent = $catalog->find( $parent_id );
        if( $parent ) {
            foreach( $parent->getAttributes() as $k => $p ) {
                if( preg_match( '/p\d+/', $k ) ) {
                    $field = $form->getChild( $k );
                    if( trim( $p ) && ! $field->getValue() ) {
                        $field->setValue( $p );
                    }
                }
            }
        }

        $this->request->setTitle( $this->t('catalog','Catalog') );
        return array(
            'breadcrumbs' => $id ? $this->adminBreadcrumbsById( $id ) : $this->adminBreadcrumbsById( $parent_id ),
            'form'        => $form,
            'cat'         => $form->getChild( 'id' )->getValue(),
        );
    }

    /**
     * Перемещение товаров и разделов
     */
    public function moveAction()
    {
        /**
         * @var CatalogModel $catalogFinder
         */
        $catalogFinder = $this->getModel('Catalog');
        // перемещение
        if( $this->request->query->has('move_list') ) {
            $list   = $this->request->query->get('move_list');
            $target = $this->request->query->get('target');
            return array('msg' => $catalogFinder->moveList($list, $target));
        }
        return array('msg' => 'Fail');
    }

    /**
     * Сохранить порядок сортировки
     */
    public function saveorderAction()
    {
        /**
         * @var CatalogModel $catalogFinder
         * @var Catalog $item
         */
        $catalogFinder = $this->getModel( 'Catalog' );

        // Сохранение позиций
        if( $save_pos = $this->request->get( 'save_pos' ) ) {
            foreach( $save_pos as $pos ) {
                $item = $catalogFinder->find( $pos[ 'key' ] );
                if( $item ) {
                    $item->pos = $pos[ 'val' ];
                    $item->save();
                }
            }
        }
        $this->redirect( $this->router->createServiceLink('catalog','admin',array('part'=>$this->request->get('part'))) );
    }

    /**
     * Меняет св-во hidden у каталога
     */
    public function hiddenAction()
    {
        /**
         * @var CatalogModel $model
         * @var Catalog $obj
         */
        $model = $this->getModel( 'Catalog' );
        $id    = $this->request->get( 'id' );
        $obj   = $this->getModel( 'Catalog' )->find( $id );

        $obj->set( 'hidden', 0 == $obj->get( 'hidden' ) ? 1 : 0 );

        $obj->save();

        return $model->getOrderHidden( $id, $obj->get( 'hidden' ) );
    }



    /**
     * Групповой аяксовый делит по id из поста
     * @return mixed
     */
    public function groupAjaxDelete()
    {
        $delete_list = $this->request->get( 'trade_delete' );
        $this->request->setAjax(true);
        $content     = 'ничего не удалено';
        if( is_array( $delete_list ) && count( $delete_list ) ) {
            $search = join( ',', $delete_list );
            if( $this->getDB()->update( $this->getModel('Catalog')->getTable(),
                    array( 'deleted'=> 1 ), "id IN ({$search})", '' )
            ) {
                $content = $search;
            }
        }
        return $content;
    }
}
