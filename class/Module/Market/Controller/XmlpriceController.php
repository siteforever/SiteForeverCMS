<?php
/**
 * Контроллер импорта прайса в виде XML
 */
namespace Module\Market\Controller;

use Sfcms_Controller;
use SimpleXMLIterator;

class XMLPriceController extends Sfcms_Controller
{
    function indexAction()
    {
        $this->request->set('template', 'inner');
        $this->request->setContent('XML Price');

        $start  = microtime(true);

        $xml_file   = ROOT.DIRECTORY_SEPARATOR.'prices.xml';

        $xml    = new SimpleXMLIterator( file_get_contents( $xml_file ) );

        $html   = array();
        $html[] = "<table><tr><th>Арт.</th><th>Наим.</th><th>Цена1</th><th>Цена2</th><th>Кол-во</th></tr>";
        foreach( $xml->children() as $trade )
        {
            $html[] = "<tr><td>{$trade->txtart}</td><td>{$trade->txtname}</td>".
                "<td>".($trade->price1?"{$trade->price1}&nbsp;{$trade->item1}/{$trade->currency1}":"&mdash;")."</td>".
                "<td>".($trade->price2?"{$trade->price2}&nbsp;{$trade->item2}/{$trade->currency2}":"&mdash;")."</td>".
                "<td>".($trade->count?$trade->count:"&mdash;")."</td></tr>";
        }
        $html[] = "</table>";

        $this->request->setContent(join($html, "\n").(round(microtime(true)-$start,4)).' sec');
    }
}