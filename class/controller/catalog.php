<?php
/**
 * Контроллер каталога
 * @author KelTanas
 */
class controller_Catalog extends controller
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
        $cat_item   = $catalog->find( $cat_id );

        if ( ! $cat_item ) {
            $this->request->addFeedback(t('Catalogue part not found with id ').$cat_id);
            return;
        }


        // хлебные крошки для каталога
        $html       = array();
        $patches    = json_decode( $catalog->get('path'), true );

        if ( is_array($patches) ) {
            foreach( $patches as $key => $path ) {
                if ( $key == 0 && $this->page['link'] != 0 ) {
                    continue;
                }
                $html[] = "<a ".href($this->page['alias'], array('cat'=>$path['id'])).">{$path['name']}</a>";
            }
        }

        //printVar($patches);
        //printVar(json_decode( $this->page['path'], true ));

        $html_page = array();
        foreach( json_decode( $this->page['path'], true ) as $path ) {
            $html_page[] = "<a ".href($path['url']).">{$path['name']}</a>";
        }
        $html = array_merge( $html_page, $html );
        $this->tpl->assign('breadcrumbs', '<div class="b-breadcrumbs">'.join(' &gt; ', $html).'</div>');

        // отрубаем breadcrumbs основной страницы
        $this->request->set('tpldata.page.path', '');

        try {
            // Если открывается раздел каталога
            if ( $cat_item['cat'] )
            {
                // количество товаров и подразделов
                $count  = $catalog->getCountByParent( $catalog->get('id'), 0 );
                $paging = $this->paging( $count, 20, $this->router->createLink( $this->page['alias'], array('cat'=>$cat_id) ) );

                $list   = $catalog->findGoodsByParent( $cat_id, $paging['limit'] );
                foreach( $list as $key => $item )
                {
                    $list[$key]['properties'] = $this->buildParamView( $cat_item, $item );
                }
                //printVar($list);

                $cats   = $catalog->findCatsByParent( $cat_id );

                $this->tpl->assign(array(
                    'category'  => $cat_item,
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

                if ( $cat_item['parent'] ) {
                    $category       = $catalog->find( $cat_item['parent'] );
                    $properties = $this->buildParamView($category, $cat_item);
                }

                $gallery_model  = $this->getModel('CatGallery');
                $gallery        = $gallery_model->findGalleryByProduct( $cat_id, 0 );

                $this->tpl->assign(array(
                    'product'   => $cat_item,
                    'properties'=> $properties,
                    'gallery'   => $gallery,
                    'user'      => $this->user,
                ));

                $this->request->setTitle(
                    ($this->page['title'] ? $this->page['title'] : $this->page['name']).
                    ' &mdash; '.$cat_item['name']
                );
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
    function buildParamView( $cat, $item )
    {
        $properties = array();

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

            $properties[ $cat['p'.$p] ] = $item['p'.$p];
        }
        return $properties;
    }

    /**
     * Редактирование / добавление раздела / товара каталога
     * @return void
     */
    function adminEdit()
    {
        $catalog = $this->getModel('Catalog');
        $catalog_gallery = $this->getModel('CatGallery');

        $id = $this->request->get('edit', FILTER_SANITIZE_NUMBER_INT);

        $type   = $this->request->get('type', FILTER_SANITIZE_NUMBER_INT);
        $parent_id = $this->request->get('add', FILTER_SANITIZE_NUMBER_INT);

        /**
         * @var form_Form
         */
        $form = $catalog->getForm();


        if ( $id ) // если раздел существует
        {
            $data       = $catalog->find( $id );
            $parent     = $data;
            $parent_id  = isset( $data['parent'] ) ? $data['parent'] : 0;
            $form->setData( $data );
        }
        elseif( $type !== false && $parent_id !== false )
        {
            $parent     = $catalog->find( $parent_id );
            $form->parent->setValue( $parent_id );
            $form->cat->setValue( $type );
        }
        else {
            die('Не указаны важные параметры');
        }

        // Если форма отправлена
        if ( $form->getPost() )
        {
            $this->setAjax();
            if ( $form->validate() )
            {
                $catalog->setData( $form->getData() );
                if ( $catalog->get('id') != 0 && $catalog->get('id') == $catalog->get('parent') ) {
                    $this->request->addFeedback(t('The section can not be in myself'));
                    return;
                }
                //printVar($form->getData());
                if ( $catalog->update() ) {
                    $this->request->addFeedback(t('Data save successfully'));
                    if ( $id ) {
                    } else {
                        reload('', array('part'=>$catalog->get('parent')));
                    }
                } else {
                    $this->request->addFeedback(t('Data not saved'));
                }
            }
            else {
                $this->request->addFeedback('Форма заполнена не правильно');
            }
            return;
        }

        // если товар
        if ( ( ! $id && $type == 0 ) || ( isset($data) && is_array($data) && $data['cat'] == 0 ) )
        {
            //$form->image->show();
            $form->icon->hide();
            $form->articul->show();
            $form->price1->show();
            $form->price2->show();
            $form->sort_view->hide();

            $parent = $catalog->find( $parent_id );
            if ( is_array( $parent ) ) {
                foreach( $parent as $k => $p ) {
                    if ( preg_match('/p\d+/', $k) ) {
                        if ( $p ) {
                            $form->getField( $k )->setLabel( $p );
                        }
                        else {
                            $form->getField( $k )->hide();
                        }
                    }
                }
            }

            if ( $id ) {
                $gallery = $catalog_gallery->findGalleryByProduct($id);
                $this->tpl->gallery = $gallery;
            }
        }
        else { // если каталог
            $icon_dir = 'files/catalog/icons';
            if ( ! is_dir( $icon_dir ) ) {
                mkdir($icon_dir, 0777, true);
            }
            $icon_list = scandir($icon_dir);
            foreach( $icon_list as $key => $item ) {
                unset( $icon_list[$key] );
                if ( preg_match( '/(\.gif|\.jpg|\.jpeg|\.png)/i', $item ) ) {
                    $icon_list[$icon_dir.'/'.$item] = $item;
                }
            }
            $form->icon->setVariants( array_merge(array(''=>'нет иконки'), $icon_list ) );
        }

        if ( ! isset($data['path']) ) {
            $data['path']   = '[]';
        }
        $this->tpl->breadcrumbs = $this->adminBreadcrumbs($data['path']);
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
        if ( $from_json =  json_decode( $path ) ) {
            foreach( $from_json as $key => $val ) {
                $bc[] =
                    '<a '.href('', array('part'=>$val->id)).'>'.$val->name.'</a>
                    <a '.href('', array('edit'=>$val->id)).'>'.icon('pencil', 'Правка').'</a>';
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
        $catalog = $this->getModel('catalog');

        // пересортировка
        if ( $this->request->get('sort') ) {
            $error = $catalog->resort();
            die( json_encode(array('error'=>$error)) );
        }

        if ( $this->request->get('move_list') ) {
            $this->request->setContent(
                $this->request->get('target', FILTER_SANITIZE_NUMBER_INT)
            );
            $this->request->setError($catalog->moveList());
            return;
        }

        // загрузка прайса
        if ( $this->request->get('price') == 'load' ) {
            return $this->loadPrice( $catalog );
        }

        if ( $this->request->get('delete') == 'group' ) {
            return $this->groupAjaxDelete();
        }

        // добавление / правка
        if ( $this->request->get('add', Request::INT) !== false || $this->request->get('edit', Request::INT) !== false )
        {
            return $this->adminEdit();
        }

        // удаление
        if ( $this->request->get('del', Request::INT) !== false )
        {
            $data = $catalog->find( $this->request->get('del', Request::INT) );
            $catalog->delete();
            redirect( 'admin/catalog', array('part'=>$data['parent']) );
        }

        // включение/выключение
        if ( $this->request->get('item') && $this->request->get('switch') )
        {
            if ( $catalog->find( $this->request->get('item') ) )
            {
                switch ( $this->request->get('switch') )
                {
                    case 'on':
                        $catalog->set('hidden', '0');
                        $switch = 'off';
                        break;
                    case 'off':
                        $catalog->set('hidden', '1');
                        $switch = 'on';
                        break;
                }
                $catalog->update();
            }
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
                redirect( 'admin/catalog', array('part'=>$catalog->get('parent')) );
            }
            return;
        }

        $part = $this->request->get('part');
        $part = $part ? $part : '0';

        $parent = $catalog->find( $part );

        // Если корневой раздел
        if ( !$parent ) {
            $parent = array('id'=>0, 'parent'=>0, 'path'=>'[]');
        }

        // Если смотрим список в товаре, то переместить на редактирование
        if ( $parent['id'] != 0 && $parent['cat'] == 0) {
            redirect('', array('edit'=>$parent['id']));
        }

        $count = $catalog->getCountByParent( $part );

        $paging = $this->paging( $count, 25, 'admin/catalog/part='.$part );

        $list = $catalog->findAllByParent( $part, $paging['limit'] );

        $this->tpl->assign(array(
            'parent'=> $parent,
            'id'    => $part,
            'part'  => $part,
            'breadcrumbs'    => $this->adminBreadcrumbs($parent['path']),
            'list'  => $list,
            'paging'=> $paging,
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