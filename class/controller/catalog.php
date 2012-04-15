<?php
/**
 * Контроллер каталога
 * @author KelTanas
 */
class Controller_Catalog extends Sfcms_Controller
{

    public function init()
    {
        $config = array(
            // сортировка товаров
            'order_list'    => array(
                ''           => 'Без сортировки',
                'name'       => 'По наименованию',
                'price1'     => 'По цене (0->макс)',
                'price1 DESC'=> 'По цене (макс->0)',
                'articul'    => 'По артикулу',
            ),
            'order_default' => 'name',
        );
        $this->config->setDefault( 'catalog', $config );
    }

    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
                'admin', 'delete', 'save', 'hidden', 'price', 'move', 'saveorder', 'category', 'trade'
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
         */
        $cat_id = $this->getCatId();

        $catalog_model = $this->getModel( 'Catalog' );
        $page_model    = $this->getModel( 'Page' );

        // без параметров
        if( ! $cat_id ) {
            $criteria            = new Db_Criteria();
            $criteria->condition = 'parent = 0 AND cat = 1 AND deleted = 0 AND hidden = 0';
            $criteria->order     = 'pos DESC';
            $list                = $catalog_model->findAll( $criteria );
            $this->tpl->assign('list', $list );

            $this->request->setContent( $this->tpl->fetch( 'catalog.category_first' ) );
            return;
        }

        $item = $catalog_model->find( $cat_id );
        if( null === $item ) {
            $this->request->addFeedback( t( 'Catalogue part not found with id ' ) . $cat_id );
            return;
        }

        // хлебные крошки для каталога
        $this->breadcrumbById( $cat_id );

        $this->request->setTitle( $item->name );


        //        $page_number    = $this->request->get('page', FILTER_SANITIZE_NUMBER_INT, 1);
        $this->tpl->assign( 'page_number', $this->request->get( 'page', FILTER_SANITIZE_NUMBER_INT, 1 ) );

        // Если открывается раздел каталога
        if( $item->cat ) {
            return $this->indexCategories( $item );
        } else {
            // Открывается товар
            return $this->indexTrade( $item );
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
    protected function indexCategories( Data_Object_Catalog $item )
    {
        // @TODO Сделать вывод товаров с указаним уровня вложенности в параметре
        //$level = 3;

        /** @var $catModel Model_Catalog */
        $catModel     = $this->getModel( 'Catalog' );
        $parent       = $catModel->find( $item->getId() );

        $categoriesId = array( $item->getId() );
        $categoriesId = array_merge( $categoriesId, $catModel->getAllChildrensIds( $item->getId() ) );

        // количество товаров
        $criteria = array(
            'cond'      => ' deleted = 0 AND hidden = 0 AND cat = 0 AND parent IN ('.implode(',', $categoriesId).') ',
        );

        $count = $catModel->count( $criteria[ 'cond' ] );

        $paging = $this->paging( $count, 10, $this->router->createLink( $this->page[ 'alias' ], array( 'id'=> $item->getId() ) ) );

        $criteria[ 'limit' ] = $paging->limit;


        $order = $this->config->get( 'catalog.order_default' );

        // Примеряем способ сортировки к списку из конфига
        $orderList = $this->config->get( 'catalog.order_list' );
        if( $orderList && is_array( $orderList ) ) {
            $set = $this->request->get( 'order' );
            if( $set && $this->config->get( 'catalog.order_list.' . $set ) ) {
                $order = $set;
                $this->request->set( 'order', $order );
            }
        }

        if( $order ) {
            $criteria[ 'order' ] = $order;
        }

        $list = $catModel->with('Gallery')->findAll( $criteria );

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

        $this->request->setContent( $this->tpl->fetch( 'catalog.goods' ) );
    }

    /**
     * Открывается товар
     * @param Data_Object_Catalog $item
     */
    protected function indexTrade( Data_Object_Catalog $item )
    {
        $cat_id        = $this->getCatId();
        $catalog_model = $this->getModel( 'Catalog' );

        $properties = array();

        if( $item->parent ) {
            $category   = $catalog_model->find( $item[ 'parent' ] );
            $properties = $this->buildParamView( $category, $item );
        }

        $gallery_model = $this->getModel( 'CatGallery' );

        $gallery = $gallery_model->findAll( array(
            'cond'      => ' cat_id = ? AND hidden = 0 ',
            'params'    => array( $cat_id ),
        ) );

        $this->tpl->assign( array(
            'item'      => $item,
            'properties'=> $properties,
            'gallery'   => $gallery,
            'user'      => $this->user,
        ) );

        $this->request->setTitle( $item[ 'name' ] );
        $this->request->setContent( $this->tpl->fetch( 'catalog.product' ) );

    }

    /**
     * Удалит раздел или товар
     */
    public function deleteAction()
    {
        /**
         * @var Data_Object_Catalog $item
         */

        $id = $this->request->get( 'id' );
        /**
         * @var Model_Catalog $catalog
         */
        $catalog = $this->getModel( 'Catalog' );

        $item = $catalog->find( $id );
        if( $item ) {
            $catalog->remove( $id );
        }
        //        var_dump($item->getAttributes());
        //        $this->adminAction();
        redirect( 'catalog/admin', array( 'part'=> $item->parent ) );
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
        $catalogFinder = $this->getModel( 'Catalog' );
        $form    = $catalogFinder->getForm();

        $this->setAjax();

        // Если форма отправлена
        if( $form->getPost() ) {
            if( $form->validate() ) {
                $object = $catalogFinder->createObject( $form->getData() );

                if( $object->getId() && $object->getId() == $object->parent ) {
                    // раздел не может быть замкнут на себя
                    return t( 'The section can not be in myself' );
                }
                if( ! $object->getId() ) {
                    $object->save();
                    reload(
                        '', array(
                            'controller'=> 'catalog',
                            'action'    => 'admin',
                            'part'      => $object->parent
                        )
                    );
                }
                $object->save();
                return t( 'Data save successfully' );
            }
            else {
                return $form->getFeedbackString();
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
            '<a href="' . $this->router->createServiceLink( 'catalog', 'admin' ) . '">Каталог</a>'
        ); // breadcrumbs

        if( $from_string = @unserialize( $path ) ) {
            if( $from_string && is_array( $from_string ) ) {
                foreach( $from_string as $val ) {
                    $bc[ ] = '<a href="'
                             . $this->router->createServiceLink( 'catalog', 'admin', array( 'part'=> $val[ 'id' ] ) )
                             . '">' . $val[ 'name' ] . '</a>'
                             . '<a href="'
                             . $this->router->createServiceLink( 'catalog', 'category', array( 'edit'=> $val[ 'id' ] ) )
                             . '">' . icon( 'pencil', 'Правка' ) . '</a>';
                }
            }
        }
        return '<div class="b-breadcrumbs">Путь: ' . join( ' &gt; ', $bc ) . '</div>';
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
     * Создание хлебных крошек для страницы каталога
     * @param $id
     */
    public function breadcrumbById( $id )
    {
        /**
         * @var Data_Object_Catalog $item
         */
        $bc   = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', 'Главная');

        $item   = $this->getModel( 'Catalog' )->find( $id );
        $path   = @unserialize( $item->path() );

        if ( is_array( $path ) ) {
            foreach( $path as $p ) {
                $bc->addPiece(
                    trim( $this->router->createLink( 'catalog', array( 'id'=> $p['id'] ) ), '/' ),
                    $p['name']
                );
            }
        }
    }


    /**
     * Действие панели администратора
     * @return mixin
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
            return;
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
                    'path'  => '[]'
                )
            );
        }

        // Если смотрим список в товаре, то переместить на редактирование
        if( $parent->getId() && ! $parent->cat ) {
            redirect( '', array( 'edit'=> $parent->getId() ) );
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
        $paging = $this->paging( $count, 25, 'admin/catalog/part=' . $part );

        $crit[ 'limit' ] = $paging->limit;
        $crit[ 'order' ] = 'cat DESC, pos DESC';

        $list = $catalogFinder->findAll( $crit );

        if ( $parent->get('path') ) {
            $breadcrumbs    = $this->adminBreadcrumbs( $parent[ 'path' ] );
        } else {
            $breadcrumbs    = $this->adminBreadcrumbsById( $parent->getId() );
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



        //        $content = $this->tpl->fetch( 'system:catalog/admin' );
//        $this->request->setContent( $content );
    }

    /**
     * Правка товара
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
        $parent_id = $this->request->get( 'add', Request::INT, 0 );

        $form = $catalogFinder->getForm();

        if( $id ) { // если раздел существует
            $item      = $catalogFinder->find( $id );
            $parent_id = $item[ 'parent' ];
            $form->setData( $item->getAttributes() );
        } else {
            $item = $catalogFinder->createObject();
            $form->getField( 'parent' )->setValue( $parent_id );
            $form->getField( 'cat' )->setValue( 0 );
        }

        // ЕСЛИ ТОВАР
        //$form->image->show();
        $form->getField( 'icon' )->hide();
        $form->getField( 'articul' )->show();
        $form->getField( 'price1' )->show();
        $form->getField( 'price2' )->show();
        $form->getField( 'sort_view' )->hide();

        //$form->top->show();
        $form->getField( 'byorder' )->show();
        $form->getField( 'absent' )->show();

        // показываем поля родителя
        $parent = $catalogFinder->find( $parent_id );

        if( file_exists( ROOT . '/protected/filters.php' ) ) {
            $filter = include( ROOT . '/protected/filters.php' );
        }

        if( $parent ) {

            $pitem = $catalogFinder->find( $parent_id );

            while( $pitem && ! $filter->getFilter( $pitem->id ) ) {
                if( $pitem->parent ) {
                    $pitem = $catalogFinder->find( $pitem->parent );
                } else {
                    $pitem = false;
                }
            }

            $fvalues = null;
            $pitem && $fvalues = $filter->getFilter( $pitem->id );

            foreach( $parent->getAttributes() as $k => $p ) {
                if( preg_match( '/p(\d+)/', $k, $m ) ) {
                    $field = $form->getField( $k );
                    trim( $p ) ? $field->setLabel( $p ) : $field->hide();

                    /**
                     * @var Sfcms_Filter_Group $fGroup
                     */
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
            $catgallery    = new Controller_CatGallery( $this->app() );
            $gallery_panel = $catgallery->getAdminPanel( $id );
            $this->tpl->assign( 'gallery_panel', $gallery_panel );
        }

        $this->tpl->assign( 'breadcrumbs', $this->adminBreadcrumbsById( $parent_id ) );
        $this->tpl->assign( 'form', $form );
        $this->tpl->assign( 'cat', $form->getField( 'id' )->getValue() );

        $this->request->setTitle( 'Каталог' );
        $this->request->setContent( $this->tpl->fetch( 'system:catalog.admin_edit' ) );
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

        $icon_dir = 'files/catalog/icons';
        if( ! is_dir( $icon_dir ) ) {
            mkdir( $icon_dir, 0777, true );
        }

        $icon_list = scandir( $icon_dir );
        foreach( $icon_list as $icon_key => $icon_item ) {
            unset( $icon_list[ $icon_key ] );
            if( preg_match( '/(\.gif|\.jpg|\.jpeg|\.png)/i', $icon_item ) ) {
                $icon_list[ $icon_dir . '/' . $icon_item ] = $icon_item;
            }
        }

        $form->getField( 'icon' )->setVariants( array_merge( array( ''=> 'нет иконки' ), $icon_list ) );

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

        $this->tpl->assign( array(
            'breadcrumbs' => $id ? $this->adminBreadcrumbsById( $id ) : $this->adminBreadcrumbsById( $parent_id ),
            'form' => $form,
            'cat'  => $form->getField( 'id' )->getValue(),
        ));

        $this->request->setTitle( 'Каталог' );
        $this->request->setContent( $this->tpl->fetch( 'system:catalog.admin_edit' ) );
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
        // пересортировка
        if( $this->request->get( 'sort' ) ) {
            $this->request->setResponseError( 0, $catalogFinder->resort() );
            return;
        }

        // Сохранение позиций
        if( $save_pos = $this->request->get( 'save_pos' ) ) {
            foreach( $save_pos as $pos ) {
                $item = $catalogFinder->find( $pos[ 'key' ] );
                if( $item ) {
                    $item->pos = $pos[ 'val' ];
                    $item->save();
                }
            }
            return;
        }
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

        $this->request->setContent(
            $model->getOrderHidden( $id, $obj->get( 'hidden' ) )
        );
    }


    /**
     * Групповой аяксовый делит по id из поста
     * @return void
     */
    public function groupAjaxDelete()
    {
        $delete_list = $this->request->get( 'trade_delete' );
        App::$ajax   = true;
        $content     = 'ничего не удалено';
        if( is_array( $delete_list ) && count( $delete_list ) ) {
            $search = join( ',', $delete_list );
            if( App::$db->update( $this->getModel('Catalog')->getTableName(),
                    array( 'deleted'=> 1 ), "id IN ({$search})", '' )
            ) {
                $content = $search;
            }
        }
        print $content;
    }
}
