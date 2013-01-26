<?php
/**
 * Модель Module
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\System\Model;

use Sfcms\Model;

use Sfcms\JqGrid\Provider;
use Module\System\Form\ModuleEdit;

class ModuleModel extends Model
{
    protected function onCreateTable()
    {
        $this->createObject( array(
            'name' => 'System', 'path' => 'Module/System', 'pos' => '0', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Page', 'path' => 'Module/Page', 'pos' => '1', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'News', 'path' => 'Module/News', 'pos' => '2', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Guestbook', 'path' => 'Module/Guestbook', 'pos' => '3', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Banner', 'path' => 'Module/Banner', 'pos' => '4', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Gallery', 'path' => 'Module/Gallery', 'pos' => '5', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Catalog', 'path' => 'Module/Catalog', 'pos' => '6', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Market', 'path' => 'Module/Market', 'pos' => '7', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Feedback', 'path' => 'Module/Feedback', 'pos' => '8', 'active' => '1',
        ) )->save();
        $this->createObject( array(
            'name' => 'Install', 'path' => 'Module/Install', 'pos' => '9', 'active' => '1',
        ) )->save();
    }

    public function getForm()
    {
        return new ModuleEdit();
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        $provider = new Provider( $this->app() );
        $provider->setModel( $this );

        $criteria = $this->createCriteria();

        $provider->setCriteria( $criteria );

        $provider->setFields(array(
            'id'    => array(
                'title' => 'Id',
                'width' => 50,
            ),
            'image' => array(
                'width' => 80,
                'sortable' => false,
                'format' => array(
                    'image' => array('width'=>50,'height'=>50),
                    'link' => array('controller'=>'material', 'action'=>'edit','id'=>':id','class'=>'edit','title'=>':name'),
                ),
            ),
            'name'  => array(
                'title' => t('material','Name'),
                'width' => 200,
                'format' => array(
                    'link' => array('controller'=>'material', 'action'=>'edit','id'=>':id','class'=>'edit','title'=>':name'),
                ),
            ),
            'active' => array(
                'title' => t('material','Active'),
                'width' => 50,
                'format' => array(
                    'bool' => array(),
                ),
            ),
        ));

        return $provider;
    }
}