<?php
/**
 * Контроллер каталога
 * @author KelTanas
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms_Controller;
use Module\Catalog\Model\CatalogModel;
use Data_Object_Catalog;
use Data_Object_ProductProperty;
use Module\Page\Model\PageModel;
use Sfcms_Model_Exception;
use Sfcms_Http_Exception;
use Sfcms\Request;
use Form_Form;
use Form_Field;
use Sfcms;
use Sfcms_Filter;
use Sfcms_Filter_Group;
use Sfcms_Filter_Collection;
use Forms_Catalog_Edit;

class CatalogController extends Sfcms_Controller
{
    public function defaults()
    {
        return array(
            'catalog',
            array(
                // сортировка товаров
                'order_list' => array(
                    ''           => 'Без сортировки',
                    'name'       => 'По наименованию',
                    'price1'     => 'По цене (0->макс)',
                    'price1-d'   => 'По цене (макс->0)',
                    'articul'    => 'По артикулу',
                ),
                'order_default' => 'name',
                'onPage' => '10',
                'level'  => 0, // < 1 output all products
            )
        );
    }

    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
                'admin', 'delete', 'save', 'hidden', 'price', 'move', 'saveorder', 'category', 'trade', 'goods'
            ),
        );
    }

    /**
     * Действие по умолчанию
     * @param string $alias
     * @return mixed
     * @throws Sfcms_Http_Exception
     */
    public function indexAction( $alias )
    {
        /**
         * @var Data_Object_Catalog $item
         * @var Model_Catalog $catalogModel
         * @var Model_Page $pageModel
         */
        $catId = $this->page->link;

        $catalogModel = $this->getModel( 'Catalog' );

        /** @var $item Data_Object_Catalog */
        $item = null;
        if ( $alias ) {
            $item = $catalogModel->find('alias = ?', array($alias));
        } elseif ( $catId ) {
            $item = $catalogModel->find( $catId );
        }

        if( null === $item ) {
            throw new Sfcms_Http_Exception(t( 'Catalogue part not found with id ' ) . $catId, 404);
        }

        if ( 0 == $item->cat ) {
            $this->getTpl()->getBreadcrumbs()->addPiece( null, $item->name );
        }
        $this->request->setTitle( $item->title );
        $this->tpl->assign( 'page_number', $this->request->get( 'page', FILTER_SANITIZE_NUMBER_INT, 1 ) );

        return $item->cat ? $this->viewCategory( $item ) : $this->viewProduct( $item );
    }

    /**
     * Вернет Cat_id запроса
     * @return int
     */
    protected function getCatId()
    {
        $result = $this->request->get( 'id', Request::INT );
        if( ! $result ) {
            $result = $this->request->get( 'cat', Request::INT );
        }
        return $result;
    }

    /**
     * Открывается категория
     * @param Data_Object_Catalog $item
     */
    protected function viewCategory( Data_Object_Catalog $item )
    {
        // @TODO Сделать вывод товаров с указаним уровня вложенности в параметре
        $level = $this->config->get( 'catalog.level' );

        /** @var $catModel Model_Catalog */
        $catModel     = $this->getModel( 'Catalog' );
        $parent       = $catModel->find( $item->getId() );

        $categoriesId = array( $item->getId() );
        if ( $level != 1 ) {
            $categoriesId = array_merge(
                $categoriesId,
                $catModel->getAllChildrensIds( $item->getId(), $level-1 )
            );
        }

        $filter = isset( $_POST['filter'] ) ? $_POST['filter'] : array();
//        $filter = $this->request->get('filter');

//        $this->log($_REQUEST,'$_REQUEST');
//        $this->log($filter,'filter');

        $manufId    = isset( $filter['manufacturer'] ) && $filter['manufacturer'] ? $filter['manufacturer'] : false;
        $materialId = isset( $filter['material'] ) && $filter['material'] ? $filter['material'] : false;
//        if ( -1 == $manufId ) {
//            $manufId = $this->app()->getSession()->get('manufacturer') ?: -1;
//        }
//        if ( 0 < $manufId ) {
//            $this->request->set('manufacturer', $manufId);
//            $this->app()->getSession()->set('manufacturer', $manufId);
//        }

        $criteria = $catModel->createCriteria();

//        $criteria->condition->condAnd( array( 'deleted' => 0, 'hidden' => 0, 'cat' => 0 ) );

        $criteria->condition = " `deleted` = 0 AND `hidden` = 0 AND `cat` = 0 ";
        if ( count($categoriesId) ) {
            $criteria->condition .= ' AND `parent` IN (?) ';
            $criteria->params[] = $categoriesId;
        }
        if ( $manufId ) {
            $criteria->condition .= ' AND `manufacturer` IN (?) ';
            $criteria->params[] = $manufId;
        }
        if ( $materialId ) {
            $criteria->condition .= ' AND `material` IN (?) ';
            $criteria->params[] = $materialId;
        }
//            . ( count( $categoriesId ) ? ' AND `parent` IN ('.implode(',',$categoriesId ) . ')' : '' )
//            . ( $manufId ? ' AND `manufacturer` = '.$manufId.' ' : '' );

        // количество товаров
        $count = $catModel->count( $criteria );

        $order = $this->config->get( 'catalog.order_default' );

//        $this->log($this->request->get( 'order' ),'order');

        // Примеряем способ сортировки к списку из конфига
        $orderList = $this->config->get( 'catalog.order_list' );
        if( $orderList && is_array( $orderList ) ) {
            if ( ! (  $set = $this->request->get( 'order' ) ) ) {
                $set = $this->app()->getSession()->get('Sort') ?: false;
            }
            if( $set && $this->config->get( 'catalog.order_list.' . $set ) ) {
                $order = $set;
                $this->request->set( 'order', $order );
                $this->app()->getSession()->set('Sort', $order);
            }
        }

        if( $order ) {
            $criteria->order = str_replace('-d', ' DESC', $order);
        }

        $paging = $this->paging(
            $count,
            $this->config->get('catalog.onPage'),
            $this->router->createLink( $parent->url )
        );

        $criteria->limit = $paging->limit;


        $list = $catModel->with('Gallery','Manufacturer','Material','Properties')->findAll( $criteria );

        // Оптимизированный список свойств
        $properties = array();
        /** @var Data_Object_Catalog $catItem */
        foreach( $list as $catItem ) {
            for( $i = 0; $i <= 9; $i ++ ) {
                $properties[ $catItem->getId() ][ $parent[ 'p' . $i ] ] = $catItem[ 'p' . $i ];
            }
        }

        $cats = $catModel->findAll( array(
                'cond'      => ' parent = ? AND cat = 1 AND deleted = 0 AND hidden = 0 ',
                'params'    => array( $item->getId() ),
                'order'     => 'pos DESC',
            )
        );

        //$cats   = $catalog->findCatsByParent( $cat_id );

        $this->tpl->assign( array(
            'parent'    => $parent,
            'properties'=> $properties,
            'manufacturers' => $this->getModel('Manufacturers')->findAllByCatalogCategories( $categoriesId ),
            'materials' => $this->getModel('Material')->findAllByCatalogCategories( $categoriesId ),
            'category'  => $item,
            'list'      => $list,
            'cats'      => $cats,
            'paging'    => $paging,
            'user'      => $this->user,
            'order_list'=> $this->config->get( 'catalog.order_list' ),
            'order_val' => $this->request->get( 'order' ),
        ) );

        return $this->tpl->fetch( 'catalog.viewcategory' );
    }

    /**
     * Открывается товар
     * @param Data_Object_Catalog $item
     */
    protected function viewProduct( Data_Object_Catalog $item )
    {
        $catalog_model = $this->getModel( 'Catalog' );

        $properties = array();

        if( $item->parent ) {
            $category   = $catalog_model->find( $item[ 'parent' ] );
            $properties = $this->buildParamView( $category, $item );
        }

        $gallery_model = $this->getModel( 'CatalogGallery' );

        $gallery = $gallery_model->findAll( array(
            'cond'      => ' cat_id = ? AND hidden = 0 ',
            'params'    => array( $item->id ),
        ) );

        $this->tpl->assign( array(
            'item'      => $item,
            'inBasket'  => $this->getBasket()->getCount( $item->id ),
            'parent'    => $item->parent ? $catalog_model->find( $item->parent ) : null,
            'properties'=> $properties,
            'gallery'   => $gallery,
            'user'      => $this->user,
        ) );

        $this->request->setTitle( $item->name );
        return $this->tpl->fetch( 'catalog.viewproduct' );
    }

    /**
     * Удалит раздел или товар
     */
    public function deleteAction()
    {
        /** @var Data_Object_Catalog $item */
        $id = $this->request->get( 'id' );
        /** @var Model_Catalog $catalog */
        $catalog = $this->getModel( 'Catalog' );
        $item = $catalog->find( $id );
        if( $item ) {
            $catalog->remove( $id );
        }
        return array('error'=>0,'msg'=>'');
    }


    /**
     * Создать список параметров
     * @param Data_Object_Catalog $cat
     * @param Data_Object_Catalog $item
     *
     * @return array
     */
    public function buildParamView( Data_Object_Catalog $cat, Data_Object_Catalog $item )
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
        $this->request->setAjax(true, Request::TYPE_JSON);

        /**
         * @var Model_Catalog $catalogFinder
         * @var Form_Field $field
         * @var Form_Form $form
         * @var Data_Object_Catalog $object
         */
        $catalogFinder  = $this->getModel( 'Catalog' );
        $form           = $catalogFinder->getForm();

        // Если форма отправлена
        if( $form->getPost() ) {
            if( $form->validate() ) {
                $object = $form->id ? $catalogFinder->find($form->id) : $catalogFinder->createObject();
                $object->attributes =  $form->getData();
                $object->save();

                // todo Выбрать список полей
                // todo Заполнить значениями из $_POST['field']
                if ( 0 === $object->cat && $object->type_id ) {
                    $propModel = $this->getModel('ProductProperty');
                    $type = $object->Type;
                    $fields = $type->Fields;
                    foreach ( $fields as $f ) {
                        /** @var $property Data_Object_ProductProperty */
                        $property = $propModel->createObject();
                        $property->product_id = $object->id;
                        $property->product_field_id = $f->id;
                        $property->pos        = $f->pos;
                        $property->set('value_'.$f->type, $_POST['field'][$f->id]);
                        $property->save();
                    }
                }

                if( $object->getId() && $object->getId() == $object->parent ) {
                    // раздел не может быть замкнут на себя
                    return array('error'=>1, t( 'The section can not be in myself' ) );
                }
                return array('error'=>0,'msg'=>t( 'Data save successfully' ));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }
        return array('error'=>1);
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
            Sfcms::html()->link(t('catalog','Catalog'),'catalog/admin')
        ); // breadcrumbs
        if( $arrPath = @unserialize( $path ) ) {
            if( $arrPath && is_array( $arrPath ) ) {
                foreach( $arrPath as $val ) {
                    $bc[ ] = Sfcms::html()->link($val['name'],'catalog/admin',array('part'=>$val['id']))
                           . Sfcms::html()->link(
                                Sfcms::html()->icon('pencil', t('Edit')),
                                'catalog/category',
                                array('edit'=>$val['id']),
                                'edit'
                            );
                }
            }
        }
        return '<ul class="breadcrumb"><li>'.t('catalog','Path').': '
                . join( '<span class="divider">&gt;</span></li><li>', $bc ) . '</li></ul>';
    }

    /**
     * Построит крошки для админки исходя из $id раздела или товара
     * @param $id
     *
     * @return string
     */
    public function adminBreadcrumbsById( $id )
    {
        /** @var Data_Object_Catalog $item */
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
         * @var Model_Catalog $catalogFinder
         * @var Data_Object_Catalog $parent
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
                throw new Sfcms_Model_Exception();
            }
            $parent = $catalogFinder->find( $part );
        } catch( Sfcms_Model_Exception $e ) {
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
        $crit[ 'order' ] = 'cat DESC, pos DESC';

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
    public function tradeAction( $edit, $add, $type )
    {
        /**
         * @var Model_Catalog $catalogFinder
         * @var Data_Object_Catalog $pitem
         * @var Form_Field $field
         * @var Forms_Catalog_Edit $form
         * @var Sfcms_Filter_Collection $filter
         * @var Sfcms_Filter $fvalues
         */

        if ( ! ( $edit || $type ) ) {
            return 'Undefined required parameters';
        }

        $catalogFinder = $this->getModel( 'Catalog' );

        $id       = $edit;
        if ( $add ) {
            $parentId = $add;
//            $this->app()->getSession()->set('catalogCategory', $add);
        } else {
            $parentId = $this->app()->getSession()->get('catalogCategory') ?: 0;
        }

        $form = $catalogFinder->getForm();

        if ( $type ) {
            $form->type_id = $type;
        }

        /** @var $item Data_Object_Catalog */
        if( $id ) { // если раздел существует
            $item      = $catalogFinder->find( $id );
            $parentId = $item[ 'parent' ];
            $form->setData( $item->getAttributes() );
        } else {
            $item = $catalogFinder->find('`name` IS NULL AND `deleted` = 1');
            if ( null === $item ) {
                $item = $catalogFinder->createObject();
                $item->deleted = 1;
                $item->save();
            }
            $id = $item->id;
            $form->id       = $item->id;
            $form->parent   = $parentId;
            $form->cat      = 0;
            $form->deleted  = 0;
        }

        // ЕСЛИ ТОВАР
        //$form->image->show();
//        $form->getField( 'icon' )->hide();
        $form->getField( 'articul' )->show();
        $form->getField( 'material' )->show();
        $form->getField( 'manufacturer' )->show();
        $form->getField( 'price1' )->show();
        $form->getField( 'price2' )->show();
        $form->getField( 'sort_view' )->hide();

        //$form->top->show();
        $form->getField( 'byorder' )->show();
        $form->getField( 'absent' )->show();

        // показываем поля родителя
        if ( $parentId ) {
            $parent = $catalogFinder->find( $parentId );
        } else {
            $parent = null;
        }

        if ( $parent ) {
            $form->applyFilter( $parentId );
            $form->applyProperties( $parent->attributes, isset( $fvalues ) ? $fvalues : null );
        } else {
            for( $i = 0; $i < 10; $i ++ ) {
                $form->getField( 'p' . $i )->hide();
            }
        }

        if( $id ) {
            $catgallery    = new GalleryController( $this->app() );
            $gallery_panel = $catgallery->getPanel( $id );
            $this->tpl->assign( 'gallery_panel', $gallery_panel );
        }

        if ( ! $item->type_id ) {
            $item->type_id = $type;
        }
        // Обработка полей из модуля полей
        if ( $item->Type ) {
            $fields = $item->Type->Fields;
        }

        $this->request->setTitle( 'Каталог' );
        return array(
//            'breadcrumbs' => $this->adminBreadcrumbsById( $parentId ),
            'form'        => $form,
            'fields'      => isset( $fields ) ? $fields : array(),
            'cat'         => $form->id,
        );
    }

    /**
     * Правка категории
     */
    public function categoryAction()
    {
        /**
         * @var Model_Catalog $catalog
         * @var Form_Field $field
         * @var Form_Form $form
         */
        $catalog = $this->getModel( 'Catalog' );

        $id        = $this->request->get( 'edit', Request::INT );
        $parent_id = $this->request->get( 'add', Request::INT, 0 );

        $form = $catalog->getForm();

        if( $id ) { // если редактировать
            $item      = $catalog->find( $id );
            $parent_id = isset( $item[ 'parent' ] ) ? $item[ 'parent' ] : 0;
            $form->setData( $item->getAttributes() );
        } else { // если новый
            $item = $catalog->createObject();
            $form->getField( 'parent' )->setValue( $parent_id );
            $form->getField( 'cat' )->setValue( 1 );
        }

        // наследуем поля родителя
        $parent = $catalog->find( $parent_id );
        if( $parent ) {
            foreach( $parent->getAttributes() as $k => $p ) {
                if( preg_match( '/p\d+/', $k ) ) {
                    $field = $form->getField( $k );
                    if( trim( $p ) && ! $field->getValue() ) {
                        $field->setValue( $p );
                    }
                }
            }
        }

        $this->request->setTitle( t('catalog','Catalog') );
        return array(
            'breadcrumbs' => $id ? $this->adminBreadcrumbsById( $id ) : $this->adminBreadcrumbsById( $parent_id ),
            'form'        => $form,
            'cat'         => $form->getField( 'id' )->getValue(),
        );
    }

    /**
     * Перемещение товаров и разделов
     */
    public function moveAction()
    {
        /**
         * @var Model_Catalog $catalogFinder
         */
        $catalogFinder = $this->getModel( 'Catalog' );
        // перемещение
        if( $this->request->get( 'move_list' ) ) {
            $this->request->setContent(
                $this->request->get( 'target', FILTER_SANITIZE_NUMBER_INT )
            );
            $this->request->setResponseError( 0, $catalogFinder->moveList() );
            return;
        }
    }

    /**
     * Сохранить порядок сортировки
     */
    public function saveorderAction()
    {
        /**
         * @var Model_Catalog $catalogFinder
         * @var Data_Object_Catalog $item
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
         * @var Model_Catalog $model
         * @var Data_Object_Catalog $obj
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
            if( App::$db->update( $this->getModel('Catalog')->getTableName(),
                    array( 'deleted'=> 1 ), "id IN ({$search})", '' )
            ) {
                $content = $search;
            }
        }
        return $content;
    }
}
