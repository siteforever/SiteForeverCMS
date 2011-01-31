<?php
/**
 * Контроллер корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class controller_Basket extends Controller
{
    function indexAction()
    {
        // создать заказ
        if ( $this->request->get('do_order') ) {
            redirect("order/create");
        }

        $cat    = $this->getModel('Catalog');

        $this->request->setTitle('Корзина');
        $this->request->setContent('Корзина');
        $this->request->set('template', 'inner');

        // добавление товара в корзину
        $basket_prod_id = $this->request->get('basket_prod_id', FILTER_SANITIZE_NUMBER_INT);

        if ( $basket_prod_id )
        {
            $count = $this->request->get('basket_prod_count', FILTER_SANITIZE_NUMBER_INT);
            $product = $cat->find( $basket_prod_id );

            $price = ($this->user->perm == USER_WHOLE) ? $product['price2'] : $product['price1'];
            $price = $price ? $price : 0;

            $details = '';
            $params = $this->request->get('p');
            if ( $params && is_array($params) && count( $params ) )
            {
                foreach( $params as $pkey => $pitem ) {
                    $details .= "$pkey:&nbsp;$pitem\n";
                }
            }

            if ( $product ) {
                $this->basket->add( $basket_prod_id, $count, $price, $details );
                $this->basket->save();
            }
            if ( $this->getAjax() ) {
                $this->tpl->assign(array(
                    'count'     => $this->basket->getCount(),
                    'summa'     => $this->basket->getSum(),
                    'number'    => $this->basket->count(),
                    'path'      => array(
                        'images'=> 'http://'.$_SERVER['HTTP_HOST'].
                                '/themes/'.$this->config->get('template.theme').'/images',
                    ),
                ));
                $this->request->setContent($this->tpl->fetch('basket.widget'));
                return;
            } else {
                redirect("catalog", array('cat'=>$product['parent']));
            }
        }

        // удаление товара из корзины
        $remove_prod_id = $this->request->get('remove_prod_id');
        if ( $remove_prod_id )
        {
            $product = $cat->find( $remove_prod_id );
            $count = $this->basket->getCount( $remove_prod_id );
            $this->basket->del( $remove_prod_id, $count );
            $this->basket->save();
            redirect("catalog", array('cat'=>$product['parent']));
        }

        // обновляем количества
        $basket_counts = $this->request->get('basket_counts');
        if ( $basket_counts && is_array($basket_counts) )
        {
            foreach( $basket_counts as $key => $prod_count )
            {
                $this->basket->setCount( $key, $prod_count );
            }
        }

        // Удалить запись
        $basket_del = $this->request->get('basket_del');
        if ( $basket_del && is_array($basket_del) )
        {
            foreach( $basket_del as $key => $prod_del ) {
                $this->basket->del( $key );
            }
        }
        if ( $basket_counts || $basket_del ) {
            $this->basket->save();
        }

        $all_count = 0;
        $all_summa = 0;

        // готовим список к выводу
        $all_product = $this->basket->getAll();
        if ( is_array( $all_product ) && count( $all_product ) )
        {
            // список id
            $all_keys = array();
            foreach( $all_product as $prod ) {
                $all_keys[] = $prod['id'];
            }
            // товары
            $goods = $cat->findGoodsById( $all_keys );
            //printVar($goods);
            foreach( $all_product as $key => &$product )
            {
                //$product['id']      = $key;
                $product['name']    = $goods[$product['id']]['name'];
                $product['summa']   = $product['price'] * $product['count'];
                $all_count += $product['count'];
                $all_summa += $product['summa'];
            }
        }

        $this->tpl->assign(array(
            'all_product'   => $all_product,
            'all_count'     => $all_count,
            'all_summa'     => $all_summa,
        ));
        $this->request->setContent($this->tpl->fetch('basket.index'));
    }


    /**
     * Добавит в корзину товар
     * @param $_REQUEST['basket_prod_id']
     * @param $_REQUEST['basket_prod_count']
     * @param $_REQUEST['basket_prod_price']
     * @param $_REQUEST['basket_prod_details']
     * @return void
     */
    function addAction()
    {
        $basket_prod_id = $this->request->get('basket_prod_id');

        if ( $basket_prod_id )
        {
            $basket_prod_count      = $this->request->get('basket_prod_count');
            $basket_prod_price      = $this->request->get('basket_prod_price');
            $basket_prod_details    = $this->request->get('basket_prod_details');

            $this->basket->add(
                $basket_prod_id,
                $basket_prod_count,
                $basket_prod_price,
                $basket_prod_details
            );

            $this->basket->save();
        }
    }
}