<?php
/**
 * Контроллер каталога
 * @author KelTanas
 */
namespace Module\Catalog\Controller;

use App;
use Sfcms_Controller;
use Model_Catalog;
use Data_Object_Catalog;
use Model_Page;
use Sfcms_Model_Exception;
use Request;
use Form_Form;
use Form_Field;
use Sfcms;
use Sfcms_Filter;
use Sfcms_Filter_Group;
use Sfcms_Filter_Collection;

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
     * @return mixed
     */
    public function indexAction()
    {
        /**
         * @var Data_Object_Catalog $item
         * @var Model_Catalog $catalogModel
         * @var Model_Page $pageModel
         */
        $catId = $this->page->link;

        $alias = $this->request->get('alias');

        $catalogModel = $this->getModel( 'Catalog' );

        /** @var $item Data_Object_Catalog */
        $item = null;
        if ( $alias ) {
            $item = $catalogModel->find('alias = ?', array($alias));
        }elseif ( $catId ) {
            $item = $catalogModel->find( $catId );
        }

        if( null === $item ) {
            return t( 'Catalogue part not found with id ' ) . $catId;
        }

        if ( 0 == $item->cat ) {
            $this->tpl->getBreadcrumbs()->addPiece( null, $item->name );
        }
        $this->request->setTitle( $item->title );
        $this->tpl->assign( 'page_number', $this->request->get( 'page', FILTER_SANITIZE_NUMBER_INT, 1 ) );

        // Если открывается раздел каталога
        if( $item->cat ) {
            return $this->viewCategory( $item );
        } else {
            // Открывается товар
            return $this->viewProduct( $item );
        }
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

        $manufId = $this->request->get('manufacturer', Request::INT);

        // количество товаров
        $criteria = $catModel->createCriteria();
        $criteria->condition = " `deleted` = 0 AND `hidden` = 0 AND cat = 0 "
            . ( count( $categoriesId ) ? ' AND `parent` IN ('.implode(',',$categoriesId ) . ')' : '' )
            . ( $manufId ? ' AND `manufacturer` = '.$manufId.' ' : '' );

        $count = $catModel->count( $criteria );

        $order = $this->config->get( 'catalog.order_default' );

        // Примеряем способ сортировки к списку из конфига
        $orderList = $this->config->get( 'catalog.order_list' );
        if( $orderList && is_array( $orderList ) ) {
            if ( ! (  $set = $this->request->get( 'order' ) ) ) {
                $set = isset( $_SESSION['Sort'] ) ? $_SESSION['Sort'] : false;
            }
            if( $set && $this->config->get( 'catalog.order_list.' . $set ) ) {
                $order = $set;
                $this->request->set( 'order', $order );
                $_SESSION['Sort'] = $order;
            }
        }

        if( $order ) {
            $criteria->order = str_replace('-d', ' DESC', $order);
        }

        $paging = $this->paging(
            $count,
            $this->config->get('catalog.onPage'),
            $this->router->createLink( $parent->url/*, array('order'=>$order)*/ )
        );

        $criteria->limit = $paging->limit;


        $list = $catModel->with('Gallery','Manufacturer')->findAll( $criteria );

        $properties = array();

        /**
         * @var Data_Object_Catalog $l
         */
        foreach( $list as $l ) {
            for( $i = 0; $i <= 9; $i ++ ) {
                $properties[ $l->getId() ][ $parent[ 'p' . $i ] ] = $l[ 'p' . $i ];
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
            'parent'    => $item->parent ? $catalog_model->find( $item->parent ) : null,
            'properties'=> $properties,
            'gallery'   => $gallery,
            'user'      => $this->user,
        ) );

        $this->request->setTitle( $item[ 'name' ] );
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
                $object = $catalogFinder->createObject();
                $object->attributes =  $form->getData();

//                $this->log( $form->getData(), 'form' );
//                $this->log( $object->attributes, 'obj' );

                if( $object->getId() && $object->getId() == $object->parent ) {
                    // раздел не может быть замкнут на себя
                    return array('error'=>1, t( 'The section can not be in myself' ) );
                }
                if( ! $object->getId() ) {
                    $object->markNew();
                } else {
                    $object->markDirty();
                }
//                $this->reload( 'catalog/admin', array( 'part' => $object->parent ), 1000 );
                return array('error'=>0,'msg'=>t( 'Data save successfully' ));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }
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
//            '<a href="' . $this->router->createServiceLink( 'catalog', 'admin' ) . '">'.t('Catalog').'</a>'
        ); // breadcrumbs
//        $this->log( $path );
        if( $arrPath = @unserialize( $path ) ) {
//            $this->log( $arrPath, 'Path' );
            if( $arrPath && is_array( $arrPath ) ) {
                foreach( $arrPath as $val ) {
                    $bc[ ] = Sfcms::html()->link($val['name'],'catalog/admin',array('part'=>$val['id']))
                            . Sfcms::html()->link( icon( 'pencil', t('Edit') ),
                                'catalog/category', array('edit'=>$val['id']), 'edit' );
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
        }

        // Если смотрим список в товаре, то переместить на редактирование
        if( $parent->getId() && ! $parent->cat ) {
            return $this->redirect( '', array( 'edit'=> $parent->getId() ) );
        }

        $crit = array();
        if( ! $filter ) {
            $crit[ 'cond' ]   = 'deleted = 0 AND parent = :parent';
            $crit[ 'params' ] = array( ':parent'=> $part );
        }
        else {
            $crit[ 'cond' ]   = 'deleted = 0 AND ( articul LIKE :filter OR name LIKE :filter )';
            $crit[ 'params' ] = array( ':filter'=> '%' . $filter . '%' );
        }

        $count  = $catalogFinder->count( $crit[ 'cond' ], $crit[ 'params' ] );
        $paging = $this->paging( $count, 10, $this->router->createServiceLink('catalog','admin',array('part'=>$part)) );

        $crit[ 'limit' ] = $paging->limit;
        $crit[ 'order' ] = 'cat DESC, pos';

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
     * @return mixed
     */
    public function tradeAction()
    {
        /**
         * @var Model_Catalog $catalogFinder
         * @var Data_Object_Catalog $pitem
         * @var Form_Field $field
         * @var Form_Form $form
         * @var Sfcms_Filter_Collection $filter
         * @var Sfcms_Filter $fvalues
         */

        $catalogFinder = $this->getModel( 'Catalog' );

        $id        = $this->request->get( 'edit', Request::INT );
        $parentId = $this->request->get( 'add', Request::INT, 0 );

        $form = $catalogFinder->getForm();

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
            $form->getField( 'id' )->setValue( $item->id );
            $form->getField( 'parent' )->setValue( $parentId );
            $form->getField( 'cat' )->setValue( 0 );
            $form->getField( 'deleted' )->setValue( 0 );
        }

        // ЕСЛИ ТОВАР
        //$form->image->show();
//        $form->getField( 'icon' )->hide();
        $form->getField( 'articul' )->show();
        $form->getField( 'manufacturer' )->show();
        $form->getField( 'price1' )->show();
        $form->getField( 'price2' )->show();
        $form->getField( 'sort_view' )->hide();

        //$form->top->show();
        $form->getField( 'byorder' )->show();
        $form->getField( 'absent' )->show();

        // показываем поля родителя
        $parent = $catalogFinder->find( $parentId );

        $filter = null;
        if( @file_exists( ROOT . '/protected/filters.php' ) ) {
            $filter = include( ROOT . '/protected/filters.php' );
        }

        if ( $parent ) {
            $pitem   = $catalogFinder->find( $parentId );
            $fvalues = null;
            if ( $filter ) {
                while ( $pitem && !$filter->getFilter( $pitem->id ) ) {
                    if ( $pitem->parent ) {
                        $pitem = $catalogFinder->find( $pitem->parent );
                    } else {
                        $pitem = false;
                    }
                }
                $pitem && $fvalues = $filter->getFilter( $pitem->id );
            }


            foreach( $parent->getAttributes() as $k => $p ) {
                if( preg_match( '/p(\d+)/', $k, $m ) ) {
                    $field = $form->getField( $k );
                    trim( $p ) ? $field->setLabel( $p ) : $field->hide();

                    /** @var Sfcms_Filter_Group $fGroup */
                    if ( $fvalues && $fGroup = $fvalues->getFilterGroup( $m[1] ) ) {
                        if (  is_array( $fGroup->getData() ) && ! $field->getValue() ) {
                            $form->getField( $k )->setValue(
                                str_ireplace(
                                    'Все|', '',
                                    implode( '|', $fGroup->getData() )
                                )
                            );
                        }
                    }
                }
            }
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


        $this->request->setTitle( 'Каталог' );
        return array(
            'breadcrumbs' => $this->adminBreadcrumbsById( $parentId ),
            'form'        => $form,
            'cat'         => $form->getField( 'id' )->getValue(),
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
     * Плоский список товаров в админке
     * @return array
     */
    public function goodsAction()
    {
        $this->request->setTitle(t('Goods'));

        $model = $this->getModel('Catalog');
        $crit = $model->createCriteria();
        $crit->condition = 'cat = ? AND deleted = 0';
        $crit->params    = array(0);
        $crit->order     = 'name';
        $count = $model->count($crit);
        $pager = $this->paging( $count, 10, 'catalog/goods' );
        $crit->limit = $pager->limit;
        $goods = $model->with('Category','Manufacturer')->findAll($crit);

        return array(
            'list'=>$goods,
            'pager'=>$pager,
        );
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
