<?php
/**
 * Загрузка прайса в каталог
 * @author: keltanas <keltanas@gmail.com>
 */
class Controller_Catalog_Price extends Sfcms_Controller
{
    /**
     * Загрузка прайса
     * @return void
     */
    public function action()
    {
        /**
         * @var \Module\Catalog\Model\CatalogModel $model
         */
        $model = $this->getModel( 'Catalog' );
        $this->request->setTitle( 'Загрузить прайслист' );

        if( isset( $_FILES[ 'xml_file' ] ) ) {

            $xmlfile = $_FILES[ 'xml_file' ];

            if( $xmlfile[ 'error' ] == UPLOAD_ERR_OK
                && $xmlfile[ 'type' ] == 'text/xml'
                && $xmlfile[ 'size' ] < 2 * 1024 * 1024
            ) {
                try {
                    $xml = new SimpleXMLElement( file_get_contents( $xmlfile[ 'tmp_name' ] ) );
                    if( $xml ) {
                        $upd_data = array();
                        $mark_del = array();
                        $xml_data = array();
                        $goods    = array();

                        $this->request->addFeedback( 'Файл загружен успешно' );

                        $html    = array();
                        $html[ ] = "<table><tr><th>Арт.</th><th>Наименование</th><th>Цена1</th><th>Цена2</th><th>Кол-во</th></tr>";

                        // индексируем прайс из XML
                        foreach( $xml->children() as $trade )
                        {
                            /**
                             * @var SimpleXMLElement $trade
                             */
                            //printVar($trade);
                            $txtart              = (string)$trade->txtart;
                            $xml_data[ $txtart ] = array(
                                'txtname'   => (string)$trade->txtname,
                                'txtart'    => $txtart,
                                'price1'    => (string)$trade->price1,
                                'item1'     => (string)$trade->item1,
                                'currency1' => (string)$trade->currency1,
                                'price2'    => (string)$trade->price2,
                                'item2'     => (string)$trade->item2,
                                'currency2' => (string)$trade->currency2,
                                'count'     => (string)$trade->count,
                            );
                        }

                        // Индексируем прайс из базы
                        $tmpgoods = $model->findAll( 'cat = 0' );
                        if( $tmpgoods ) {
                            foreach( $tmpgoods as $trade ) {
                                $goods[ $trade[ 'articul' ] ] = $trade;
                                if( ! isset( $xml_data[ $trade[ 'articul' ] ] ) ) {
                                    $mark_del[ $trade[ 'articul' ] ] = $trade;
                                }
                            }
                        }

                        // определяем операции
                        foreach( $xml_data as $trade )
                        {
                            $good        = $goods[ $trade[ 'txtart' ] ];
                            $upd_data[ ] = array(
                                'id'        => isset( $good[ 'id' ] ) ? $good[ 'id' ] : 0,
                                'name'      => $trade[ 'txtname' ],
                                'path'      => isset( $good[ 'path' ] ) ? $good[ 'path' ] : '',
                                'text'      => isset( $good[ 'text' ] ) ? $good[ 'text' ] : '',
                                'articul'   => $trade[ 'txtart' ],
                                'price1'    => isset( $trade[ 'price1' ] )
                                    ? number_format( $trade[ 'price1' ], 2, '.', '' ) : '0.00',
                                'price2'    => isset( $trade[ 'price2' ] )
                                    ? number_format( $trade[ 'price2' ], 2, '.', '' ) : '0.00',
                                'hidden'    => isset( $good[ 'hidden' ] ) ? $good[ 'hidden' ] : 1,
                            );

                            $html[ ] = "<tr><td>{$trade->txtart}</td><td>{$trade->txtname}</td>" .
                                       "<td>" . ( $trade->price1
                                ? "{$trade->price1}&nbsp;{$trade->item1}/{$trade->currency1}" : "&mdash;" ) . "</td>" .
                                       "<td>" . ( $trade->price2
                                ? "{$trade->price2}&nbsp;{$trade->item2}/{$trade->currency2}" : "&mdash;" ) . "</td>" .
                                       "<td>" . ( $trade->count ? $trade->count : "&mdash;" ) . "</td></tr>";
                        }
                        $html[ ] = "</table>";

                        App::$db->insertUpdateMulti( DBCATALOG, $upd_data );

                        $this->tpl->assign( array(
                            'xml_list'  => join( "\n", $html ),
                            'mark_del'  => $mark_del,
                        ) );
                        //$this->request->addFeedback('Отмечено для добавления: '.count($ins_data));
                        $this->request->addFeedback( 'Отмечено для обновления: ' . count( $upd_data ) );
                        $this->request->addFeedback( 'Не содержатся в прайсе: ' . count( $mark_del ) );

                    }
                } catch( Exception $e ) {
                    if( substr( $e->getMessage(), 0, 16 ) == 'SimpleXMLElement' ) {
                        $this->request->addFeedback( 'Файл загружен. Ошибка в XML структуре' );
                    } else {
                        //throw new Exception($e->getMessage(), $e->getCode());
                        $this->request->addFeedback( $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
                    }
                }
            } else {
                $this->request->addFeedback( 'Ошибка загрузки файла' );
            }
        }

        $this->render('catalog.load_price');
    }

}
