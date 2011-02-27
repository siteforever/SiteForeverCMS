<?php
/**
 * Контроллер каталога
 * @author KelTanas
 */
class Controller_Catalog extends Controller
{
    function init()
    {
        $config = array(
            // сортировка товаров
            'order_list'    => array(
                ''      => 'Без сортировки',
                'name'  => 'По наименованию',
                'price1'=> 'По цене (0->макс)',
                'price1 DESC'=> 'По цене (макс->0)',
                'articul'=>'По артикулу',
            ),
            'order_default' => 'name',
        );
        $this->config->setDefault('catalog', $config);
    }



    /**
     * Действие по умолчанию
     * @return void
     */
    function indexAction()
    {
        $cat_id = $this->request->get('cat', FILTER_SANITIZE_NUMBER_INT);

        // Пытаемся получить из страницы, если link не 0
        if ( ! $cat_id && $this->page['link'] ) {
            $cat_id = $this->page['link'];
            $this->request->set('cat', $cat_id);
        }

        // без параметров
        if ( ! $cat_id ) {
            return;
        }

        $catalog    = $this->getModel('Catalog');
        $item       = $catalog->find( $cat_id );

        if ( ! $item ) {
            $this->request->addFeedback(t('Catalogue part not found with id ').$cat_id);
            return;
        }


        // хлебные крошки для каталога
        $html       = array();
        $pathes    = @unserialize( $item->path );

        if ( is_array($pathes) ) {
            foreach( $pathes as $key => $path ) {
                if ( $key == 0 && $this->page['link'] != 0 ) {
                    continue;
                }
                $html[] = "<a ".href($this->page['alias'], array('cat'=>$path['id'])).">{$path['name']}</a>";
            }
        }

        $html_page = array();
        $page_pathes    = @unserialize( $this->page['path'] );

        if( $page_pathes && is_array( $page_pathes ) ) {
            foreach( $page_pathes as $path ) {
                $html_page[] = "<a ".href($path['url']).">{$path['name']}</a>";
            }
        }
        $html = array_merge( $html_page, $html );
        $this->tpl->assign('breadcrumbs', '<div class="b-breadcrumbs">'.join(' &gt; ', $html).'</div>');

        // отрубаем breadcrumbs основной страницы
        $this->request->set('tpldata.page.path', '');

        $page_number    = $this->request->get('page');
        $this->tpl->page_number = $page_number ? $page_number : '1';

        try {
            // Если открывается раздел каталога
            if ( $item->cat )
            {
                $parent = $catalog->find( $item->getId() );

                // количество товаров
                $criteria   = array(
                    'cond'      => ' parent = ? AND deleted = 0 AND hidden = 0 AND cat = 0 ',
                    'params'    => array($item->getId()),
                );

                $count  = $catalog->count( $criteria['cond'], $criteria['params'] );
                
                $paging = $this->paging( $count, 10, $this->router->createLink( $this->page['alias'], array('cat'=>$item->getId()) ) );

                $criteria['limit']  = $paging->limit;


                $order = $this->config->get('catalog.order_default');

                // Примеряем способ сортировки к списку из конфига
                $order_list = $this->config->get('catalog.order_list');
                if ( $order_list && is_array($order_list) ) {
                    $set = $this->request->get('order');
                    if ( $set && $this->config->get('catalog.order_list.'.$set) ) {
                        $order = $set;
                    }
                    else {
                        $order  = reset( array_keys($order_list) );
                    }
                    $this->request->set('order', $order);
                }

                if ( $order ) {
                    $criteria['order']  = $order;
                }

                $list   = $catalog->findAll( $criteria );

                $properties = array();

                foreach ( $list as $l ) {
                    for ( $i = 0; $i <= 9; $i++ ) {
                        $properties[ $l->getId() ][ $parent['p'.$i] ]  = $l['p'.$i];
                    }
                }

                $cats   = $catalog->findAll(array(
                    'cond'      => ' parent = ? AND cat = 1 AND deleted = 0 AND hidden = 0 ',
                    'params'    => array($item->getId()),
                ));

                //$cats   = $catalog->findCatsByParent( $cat_id );

                $this->tpl->assign(array(
                    'parent'    => $parent,
                    'properties'=> $properties,
                    'category'  => $item,
                    'list'      => $list,
                    'cats'      => $cats,
                    'paging'    => $paging,
                    'user'      => $this->user,
                    'order_list'=> $this->config->get('catalog.order_list'),
                    'order_val' => $this->request->get('order'),
                ));

                $this->request->setContent( $this->tpl->fetch('catalog.goods') );
            }
            else {
                // Открывается товар

                $properties = array();

                if ( $item->parent ) {
                    $category       = $catalog->find( $item['parent'] );
                    $properties = $this->buildParamView($category, $item);
                }

                $gallery_model  = $this->getModel('CatGallery');
                //$gallery        = $gallery_model->findGalleryByProduct( $cat_id, 0 );

                $gallery    = $gallery_model->findAll(array(
                    'cond'      => ' cat_id = ? AND hidden = 0 ',
                    'params'    => array( $cat_id ),
                ));

                $this->tpl->assign(array(
                    'item'      => $item,
                    'properties'=> $properties,
                    'gallery'   => $gallery,
                    'user'      => $this->user,
                ));

                $this->request->setTitle( $this->page['title'] . ' &mdash; '.$item['name'] );
                $this->request->setContent( $this->tpl->fetch('catalog.product') );
            }
        } catch ( Exception $e ) {
            $this->request->setContent( $e->getMessage().'<br />'.$e->getFile().' in '.$e->getLine() );
        }
    }





    /**
     * Создать список параметров
     * @param array $cat
     * @param array $item
     * @return array
     */
    function buildParamView( Data_Object_Catalog $cat, Data_Object_Catalog $item )
    {
        $properties = array( $item->getId() => array() );

        for ( $p = 0; $p < 10; $p ++ )
        {
            if ( $cat['p'.$p] == '' ) {
                continue;
            }

            $item['p'.$p] = trim($item['p'.$p],'; ');

            if ( strpos( $item['p'.$p], ';' ) !== false )
            {
                $par_list = explode(';', $item['p'.$p]);
                $html = array("<select name='p[{$cat['p'.$p]}]'>");
                foreach ( $par_list as $par_key => $par_val ) {
                    $html[] = "<option value='{$par_val}'>{$par_val}</option>";
                }
                $html[] = "</select>";
                $item['p'.$p] = join("\n", $html);
            }
            elseif ( $item['p'.$p] != '' ) {
                $val = $item['p'.$p];
                $item['p'.$p] = $val."<input type='hidden' name='p[{$cat['p'.$p]}]' value='{$val}' />";
            }
            else {
                continue;
            }

            $properties[ $item->getId() ][ $cat['p'.$p] ] = $item[ 'p'.$p ];

            $item->markClean();
        }
        return $properties;
    }



    /**
     * Редактирование / добавление раздела / товара каталога
     * @return void
     */
    function adminEdit()
    {
        /**
         * @var Model_Catalog $catalog
         */
        $catalog = $this->getModel('Catalog');
        $catalog_gallery = $this->getModel('CatGallery');

        $id = $this->request->get('edit', Request::INT);

        $type   = $this->request->get('type', Request::INT);
        $parent_id = $this->request->get('add', Request::INT);

        /**
         * @var form_Form
         */
        $form = $catalog->getForm();


        if ( $id !== "" ) // если раздел существует
        {
            $item       = $catalog->find( $id );

            $parent_id  = isset( $item['parent'] ) ? $item['parent'] : 0;
            $form->setData( $item->getAttributes() );
        }
        elseif( $type !== "" && $parent_id !== "" )
        {
            $parent     = $catalog->find( $parent_id );

            $form->parent   = $parent_id;
            $form->cat      = $type;
        }
        else {
            $this->request->addFeedback('Не указаны важные параметры');
            return;
        }

        // Если форма отправлена
        if ( $form->getPost() )
        {
            $this->setAjax();
            if ( $form->validate() )
            {
                $object = $catalog->createObject( $form->getData() );

                if ( $object->getId() && $object->getId() == $object->parent ) {
                    // раздел не может быть замкнут на себя
                    $this->request->addFeedback(t('The section can not be in myself'));
                    return;
                }

                $this->request->addFeedback(t('Data save successfully'));

                if ( ! $object->getId() ) {
                    $catalog->update( $object );
                    reload('', array('part'=>$object->parent));
                }

                $catalog->update( $object );
            }
            else {
                $this->request->addFeedback($form->getFeedbackString());
            }
            return;
        }

        // если товар
        if (    ! ( $id || $type ) ||
                ( isset($item) && $item instanceof Data_Object_Catalog && $item->cat == 0 )
        ) {
            //$form->image->show();
            $form->getField('icon')->hide();
            $form->getField('articul')->show();
            $form->getField('price1')->show();
            $form->getField('price2')->show();
            $form->getField('sort_view')->hide();

            //$form->top->show();
            $form->getField('byorder')->show();
            $form->getField('absent')->show();

            $parent = $catalog->find( $parent_id );
            if ( $parent ) {
                foreach( $parent->getAttributes() as $k => $p ) {
                    if ( preg_match('/p\d+/', $k) ) {
                        $field  = $form->getField( $k );
                        trim($p) ? $field->setLabel( $p ) : $field->hide();
                    }
                }
            }

            if ( $id ) {
                $gallery    = $catalog_gallery->findAll(array(
                    'cond'  => ' cat_id = ? ',
                    'params'=> array($id),
                ));
                $this->tpl->gallery = $gallery;
            }
        }
        else { // если каталог

            $icon_dir = 'files/catalog/icons';
            if ( ! is_dir( $icon_dir ) ) {
                mkdir($icon_dir, 0777, true);
            }
            $icon_list = scandir($icon_dir);
            foreach( $icon_list as $icon_key => $icon_item ) {
                unset( $icon_list[$icon_key] );
                if ( preg_match( '/(\.gif|\.jpg|\.jpeg|\.png)/i', $icon_item ) ) {
                    $icon_list[$icon_dir.'/'.$icon_item] = $icon_item;
                }
            }
            $form->getField('icon')->setVariants( array_merge(array(''=>'нет иконки'), $icon_list ) );
        }

        $this->tpl->breadcrumbs = $this->adminBreadcrumbs($item['path']);
        $this->tpl->form        = $form;
        $this->tpl->cat         = $form->id;

        $this->request->setTitle('Каталог');
        $this->request->setContent($this->tpl->fetch('system:catalog.admin_edit'));
    }

    /**
     * Генерит хлебные крошки для админки каталога
     * @param json $path
     * @return string
     */
    function adminBreadcrumbs( $path )
    {
        $bc = array('<a '.href('').'>Каталог</a>'); // breadcrumbs

        if ( $from_string =  @unserialize( $path ) ) {
            if ( $from_string && is_array( $from_string ) ) {
                foreach( $from_string as $key => $val ) {
                    $bc[] =
                            '<a '.href('', array('part'=>$val['id'])).'>'.$val['name'].'</a>
                    <a '.href('', array('edit'=>$val['id'])).'>'.icon('pencil', 'Правка').'</a>';
                }
            }
        }
        return '<div class="b-breadcrumbs">Путь: '.join(' &gt; ', $bc).'</div>';
    }


    /**
     * Действие панели администратора
     * @return void
     */
    function adminAction()
    {
        /**
         * @var model_Catalog $catalog
         */
        $catalog = $this->getModel('Catalog');

        $filter = trim( $this->request->get('goods_filter') );
        if ( $filter ) {
            $filter  = preg_replace('/[^\d\wа-яА-Я]+/u', '%', $filter);
            $filter  = str_replace(array('%34', '&#34;'), '', $filter);
            $filter  = preg_replace( '/[ %]+/u', '%', $filter );
            $filter  = trim( $filter, '%' );
        }

        // пересортировка
        if ( $this->request->get('sort') ) {
            $this->request->setResponseError( 0, $catalog->resort() );
            return;
        }

        // перемещение
        if ( $this->request->get('move_list') ) {
            $this->request->setContent(
                $this->request->get('target', FILTER_SANITIZE_NUMBER_INT)
            );
            $this->request->setResponseError( 0, $catalog->moveList() );
            return;
        }

        // Сохранение позиций
        if ( $save_pos = $this->request->get('save_pos') ) {
            foreach ( $save_pos as $pos ) {
                $item   = $catalog->find( $pos['key'] );
                if ( $item ) {
                    $item->pos  = $pos['val'];
                }
            }
            return;
        }

        // загрузка прайса
        if ( $this->request->get('price') == 'load' ) {
            return $this->loadPrice( $catalog );
        }

        if ( $this->request->get('delete') == 'group' ) {
            return $this->groupAjaxDelete();
        }
        //print 'Work '.__FILE__.':'.__LINE__;

        // добавление / правка
        if (    $this->request->get('add', FILTER_SANITIZE_NUMBER_INT ) !== '' ||
                $this->request->get('edit', FILTER_SANITIZE_NUMBER_INT ) !== ''
        ) {
            return $this->adminEdit();
        }

        //print 'Work '.__FILE__.':'.__LINE__;

        // удаление
        if ( $del_id = $this->request->get('del', FILTER_SANITIZE_NUMBER_INT) )
        {
            $item = $catalog->find( $del_id );
            if ( $item )
                $catalog->delete( $item->getId() );
            redirect( 'admin/catalog', array('part'=>$item->parent) );
        }

        // включение/выключение
        if ( $this->request->get('item') && $this->request->get('switch') )
        {
            if ( $item = $catalog->find( $this->request->get('item') ) )
            {
                switch ( $this->request->get('switch') )
                {
                    case 'on':
                        $item->hidden   = 0;
                        $switch = 'off';
                        break;
                    case 'off':
                        $item->hiden    = 1;
                        $switch = 'on';
                        break;
                }
                //$catalog->update();
                if ( $this->getAjax() ) {
                    die(json_encode(array(
                        'error' => '0',
                        'href'  => $this->router->createLink('admin/catalog', array(
                            'item'      => $this->request->get('item'),
                            'switch'    => $switch,
                        )),
                        'img'   => $switch == 'on' ? icon('lightbulb_off', 'Включить') : icon('lightbulb', 'Выключить'),
                    )));
                } else {
                    redirect( 'admin/catalog', array('part'=>$item->parent ) );
                }
            }
            return;
        }

        $part = $this->request->get('part');
        $part = $part ? $part : '0';

        try {
            $parent = $catalog->find( $part );
        } catch ( ModelException $e ) {
            $parent = null;
        }

        // Если корневой раздел
        if ( !$parent ) {
            $parent = $catalog->createObject(array('id'=>0, 'parent'=>0, 'path'=>'[]'));
        }

        // Если смотрим список в товаре, то переместить на редактирование
        if ( $parent->getId() && ! $parent->cat ) {
            redirect( '', array('edit'=>$parent->getId() ));
        }

        $crit   = array();
        if ( ! $filter ) {
            $crit['cond']   = 'deleted = 0 AND parent = :parent';
            $crit['params'] = array(':parent'=>$part);
        } else {
            $crit['cond']   = 'deleted = 0 AND articul LIKE :filter';
            $crit['params'] = array(':filter'=>'%'.$filter.'%');
        }

        $count  = $catalog->count( $crit['cond'], $crit['params'] );
        $paging = $this->paging( $count, 25, 'admin/catalog/part='.$part );

        $crit['limit']  = $paging->limit;
        $crit['order']  = 'cat DESC, pos DESC';

        $list   = $catalog->findAll( $crit );

        /*if ( ! $filter ) {

            $count = $catalog->getCountByParent( $part );

            $paging = $this->paging( $count, 25, 'admin/catalog/part='.$part );

            $list = $catalog->findAllByParent( $part, $paging['limit'] );

        }
        else {

            $list  = $catalog->findAllFiltered( $filter );

        }*/

        $this->tpl->assign(array(
            'filter'    => trim( $this->request->get('goods_filter') ),
            'parent'    => $parent,
            'id'        => $part,
            'part'      => $part,
            'breadcrumbs'    => $this->adminBreadcrumbs($parent['path']),
            'list'      => $list,
            'paging'    => $paging,
            'moving_list'=>$catalog->getCategoryList(),
        ));

        
        $content = $this->tpl->fetch('system:catalog/admin');

        $this->request->setTitle('Каталог');
        $this->request->setContent($content);
    }


    /**
     * Загрузка прайса
     * @return void
     */
    function loadPrice( model_Catalog $model )
    {
        $this->request->setTitle('Загрузить прайслист');

        if ( isset( $_FILES['xml_file'] ) ) {

            $xmlfile    = $_FILES['xml_file'];

            if (    $xmlfile['error']   == UPLOAD_ERR_OK &&
                    $xmlfile['type']    == 'text/xml' &&
                    $xmlfile['size']    < 2*1024*1024 )
            {
                try {
                    $xml    = new SimpleXMLElement( file_get_contents( $xmlfile['tmp_name'] ) );
                    if ( $xml ) {
                        $upd_data   = array();
                        $mark_del   = array();
                        $xml_data   = array();
                        $goods      = array();

                        $this->request->addFeedback('Файл загружен успешно');

                        $html   = array();
                        $html[] = "<table><tr><th>Арт.</th><th>Наименование</th><th>Цена1</th><th>Цена2</th><th>Кол-во</th></tr>";

                        // индексируем прайс из XML
                        foreach( $xml->children() as $trade )
                        {
                            /**
                             * @var SimpleXMLElement $trade
                             */
                            //printVar($trade);
                            $txtart = (string) $trade->txtart;
                            $xml_data[ $txtart ] = array(
                                'txtname'   => (string) $trade->txtname,
                                'txtart'    => $txtart,
                                'price1'    => (string) $trade->price1,
                                'item1'     => (string) $trade->item1,
                                'currency1' => (string) $trade->currency1,
                                'price2'    => (string) $trade->price2,
                                'item2'     => (string) $trade->item2,
                                'currency2' => (string) $trade->currency2,
                                'count'     => (string) $trade->count,
                            );
                        }

                        // Индексируем прайс из базы
                        $tmpgoods  = $model->findAll('cat = 0');
                        if ( $tmpgoods ) {
                            foreach ( $tmpgoods as $trade ) {
                                $goods[ $trade['articul'] ] = $trade;
                                if ( ! isset( $xml_data[ $trade['articul'] ] ) ) {
                                    $mark_del[ $trade['articul'] ]  = $trade;
                                }
                            }
                        }

                        // определяем операции
                        foreach( $xml_data as $trade )
                        {
                            $good   = $goods[ $trade['txtart'] ];
                            $upd_data[] = array(
                                'id'        => isset($good['id']) ? $good['id'] : 0,
                                'name'      => $trade['txtname'],
                                'path'      => isset($good['path']) ? $good['path'] : '',
                                'text'      => isset($good['text']) ? $good['text'] : '',
                                'articul'   => $trade['txtart'],
                                'price1'    => isset($trade['price1']) ? number_format( $trade['price1'], 2, '.', '' ) : '0.00',
                                'price2'    => isset($trade['price2']) ? number_format( $trade['price2'], 2, '.', '' ) : '0.00',
                                'hidden'    => isset($good['hidden']) ? $good['hidden'] : 1,
                            );

                            $html[] = "<tr><td>{$trade->txtart}</td><td>{$trade->txtname}</td>".
                                    "<td>".($trade->price1?"{$trade->price1}&nbsp;{$trade->item1}/{$trade->currency1}":"&mdash;")."</td>".
                                    "<td>".($trade->price2?"{$trade->price2}&nbsp;{$trade->item2}/{$trade->currency2}":"&mdash;")."</td>".
                                    "<td>".($trade->count?$trade->count:"&mdash;")."</td></tr>";
                        }
                        $html[] = "</table>";

                        App::$db->insertUpdateMulti( DBCATALOG, $upd_data );

                        $this->tpl->assign(array(
                            'xml_list'  => join("\n",$html),
                            'mark_del'  => $mark_del,
                        ));
                        //$this->request->addFeedback('Отмечено для добавления: '.count($ins_data));
                        $this->request->addFeedback('Отмечено для обновления: '.count($upd_data));
                        $this->request->addFeedback('Не содержатся в прайсе: '.count($mark_del));

                    }
                } catch ( Exception $e ) {
                    if ( substr( $e->getMessage(), 0, 16 ) == 'SimpleXMLElement' ) {
                        $this->request->addFeedback('Файл загружен. Ошибка в XML структуре');
                    } else {
                        //throw new Exception($e->getMessage(), $e->getCode());
                        $this->request->addFeedback($e->getMessage().' in '.$e->getFile().':'.$e->getLine());
                    }
                }
            } else {
                $this->request->addFeedback('Ошибка загрузки файла');
            }
        }

        $this->request->setContent( $this->tpl->fetch('catalog.load_price') );
    }

    /**
     * Групповой аяксовый делит по id из поста
     * @return void
     */
    function groupAjaxDelete()
    {
        $delete_list    = $this->request->get('trade_delete');
        App::$ajax  = true;
        $content    = 'ничего не удалено';
        if ( is_array( $delete_list ) && count( $delete_list ) ) {
            $search = join(',', $delete_list);
            if ( App::$db->update(DBCATALOG, array('deleted'=>1), "id IN ({$search})", '') ) {
                $content    = $search;
            }
        }
        print $content;
    }
}