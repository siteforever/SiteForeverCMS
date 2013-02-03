<?php
/**
 * Экспорт в формат YML
 * @author: keltanas
 * @link  http://siteforever.ru
 * @link http://partner.market.yandex.ru/legal/tt/
 */
namespace Sfcms\Yandex;

use App;
use Data_Collection;
use Data_Object_Catalog;
use DOMImplementation;
use DOMDocument;

class Yml
{
    /** @var Data_Collection */
    private $collection;

    /** @var Data_Collection */
    private $categories;

    /** @var DOMDocument */
    private $dom;

    /** @var App */
    private $app;

    public function __construct( App $app )
    {
        $this->app = $app;
    }

    public function setCollection( Data_Collection $collection )
    {
        $this->collection = $collection;
    }

    public function setCategories( Data_Collection $collection )
    {
        $this->categories = $collection;
    }

    private function prepare()
    {
        $imp = new DOMImplementation();

        $dtd = $imp->createDocumentType('yml_catalog','','shops.dtd');

        $this->dom = $imp->createDocument(null,null,$dtd);

        $this->dom->version = '1.0';
        $this->dom->encoding = 'utf-8';

        $this->dom->appendChild( $catalog = $this->dom->createElement('yml_catalog') );
        $dom = $this->dom;

        $catalog->setAttribute('date', strftime('%Y-%m-%d %H:%M'));

        $shop = $dom->createElement('shop');
        $catalog->appendChild( $shop );


        $config = $this->app->getConfig();
        $shop->appendChild( $dom->createElement('name', $config->get('sitename')) );
        $shop->appendChild( $dom->createElement('company', $config->get('sitename')) );
        $shop->appendChild( $dom->createElement('url', $config->get('siteurl')) );
        $shop->appendChild( $dom->createElement('platform', 'SiteForeverCMS') );
        $shop->appendChild( $dom->createElement('version', '0.4.1') );
        $shop->appendChild( $dom->createElement('agency', 'Firetroop') );
        $shop->appendChild( $dom->createElement('email', 'keltanas@gmail.com') );

        $currencies = $dom->createElement('currencies');
        $shop->appendChild( $currencies );
        $currency = $dom->createElement('currency');
        $currency->setAttribute('id','RUR');
        $currency->setAttribute('rate', 1);
        $currencies->appendChild( $currency );

        $categories = $dom->createElement('categories');
        $shop->appendChild($categories);
        array_map(function( Data_Object_Catalog $obj) use ( $dom, $categories ) {
            $category = $dom->createElement('category', $obj->name);
            $category->setAttribute('id', $obj->id);
            if ( $obj->parent ) {
                $category->setAttribute('parentId', $obj->parent);
            }
            $categories->appendChild( $category );
        },iterator_to_array($this->categories));

        $offers = $dom->createElement('offers');
        $shop->appendChild( $offers );
        array_map(function( Data_Object_Catalog $obj ) use ( $dom, $offers, $config ) {
            $offer = $dom->createElement('offer');
            $offers->appendChild( $offer );
            $offer->setAttribute('id', $obj->id);
            // Статус доступности товара — в наличии/на заказ
            $offer->setAttribute('available', 'true');
//            $offer->setAttribute('type', 'vendor.model');
            $offer->appendChild( $dom->createElement('url', $config->get('siteurl').'/'.$obj->getUrl()) );
            $offer->appendChild( $dom->createElement('price', $obj->getPrice()) );
            $offer->appendChild( $dom->createElement('currencyId', 'RUR') );
            $offer->appendChild( $dom->createElement('categoryId', $obj->parent) );
            if ( $obj->getImage() ) {
                $offer->appendChild( $dom->createElement('picture', $config->get('siteurl').$obj->getImage()) );
            }

            // Элемент описывает возможность приобрести товар в точке продаж без предварительного заказа по интернету. Если для данного товара предусмотрена такая возможность, используется значение "true". В противном случае — "false".
//            $offer->appendChild( $dom->createElement('store', false) );
            // Элемент характеризует наличие самовывоза (возможность предварительно заказать товар и забрать его в точке продаж). Если предусмотрен самовывоз данного товара, используется значение "true". В противном случае — "false".
            $offer->appendChild( $dom->createElement('pickup', 'true') );
            // Элемент, обозначающий возможность доставить соответствующий товар. "false" данный товар не может быть доставлен. "true" товар доставляется на условиях, которые указываются в партнерском интерфейсе http://partner.market.yandex.ru на странице "редактирование".
            $offer->appendChild( $dom->createElement('delivery', 'true') );
            // Стоимость доставки данного товара в Своем регионе
            $offer->appendChild( $dom->createElement('local_delivery_cost', $obj->getPrice() > 5000 ? 0 : 300 ) );
            // Код товара (указывается код производителя)
//            $offer->appendChild( $dom->createElement('vendorCode', '') );

            $offer->appendChild( $dom->createElement('name', $obj->name) );
            if ( $obj->manufacturer ){
                $offer->appendChild( $dom->createElement('vendor', $obj->Manufacturer->name) );
//                $offer->appendChild( $dom->createElement('vendorCode', $obj->Manufacturer->id) );
            }

            // Описание товарного предложения
            if ( trim( strip_tags($obj->text) ) ){
                $offer->appendChild( $dom->createElement(
                    'description',
                    html_entity_decode( $obj->text, ENT_QUOTES, 'UTF-8' )
                ));
            }

            for ( $i = 0; $i<=9; $i++ ) {
                $property = $obj->getProperty( $i );
                if ( $property['name'] && $property['value'] ) {
                    $param = $dom->createElement('param', $property['value']);
                    $param->setAttribute('name', $property['name']);
                    $offer->appendChild( $param );
                }
            }

        }, iterator_to_array( $this->collection ));
    }

    public function output()
    {
        $this->prepare();
        $this->dom->formatOutput = true;
        return $this->dom->saveXML();
    }
}
