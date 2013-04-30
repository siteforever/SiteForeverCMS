<?php
use Sfcms\Model;
use Sfcms\JqGrid\Provider;
/**
 * JqGrid data provider test
 */
class Sfcms_JqGrid_PrividerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    private $model;

    private $criteria;

    protected function setUp()
    {
        $this->model = Model::getModel('Catalog');
    }


    public function testGetJson()
    {
        App::$request->set('rows',10);
        $provider = new Provider( App::getInstance() );
        $criteria = $this->model->createCriteria();
        $criteria->condition = 'cat = 0 AND deleted = 0 AND hidden = 0 AND protected <= 0';

        $provider->setCriteria( $criteria );
        $provider->setModel( $this->model );
        $provider->setFields(array(
            'id'=>'Id',
            'name'=>'Name',
            'manufacturer'=>array('title'=>'Manufacturer','value'=>'Manufacturer.name'),
            'price'=>'Price',
        ));

        $this->assertEquals(
            '{' .'"page":1,'
                .'"total":2,'
                .'"records":"12",'
                .'"rows":['
                    .'{"id":"7","cell":["7","HTC Evo 3D","HTC","15000.00"]},'
                    .'{"id":"8","cell":["8","Jeep Cheerokee","Jeep","1500.00"]},'
                    .'{"id":"9","cell":["9","HTC One X","HTC","17000.00"]},'
                    .'{"id":"10","cell":["10","HTC Sensation","HTC","18000.00"]},'
                    .'{"id":"11","cell":["11","iPhone 4S","Apple","0.00"]},'
                    .'{"id":"12","cell":["12","iPhone 3GS","Apple","0.00"]},'
                    .'{"id":"13","cell":["13","iPhone 4","Apple","0.00"]},'
                    .'{"id":"14","cell":["14","Nokia 500","Nokia","0.00"]},'
                    .'{"id":"15","cell":["15","Nokia N9","Nokia","0.00"]},'
                    .'{"id":"33","cell":["33","TERRA 918 disk","Forward","9200.00"]}'
                .']'
                //.',"userdata":{"amount":3220,"tax":342,"total":3564,"name":"Totals:"}'
            .'}',
            $provider->getJsonData()
        );
    }
}
