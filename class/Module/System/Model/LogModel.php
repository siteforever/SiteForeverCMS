<?php
namespace Module\System\Model;

use Sfcms\Model;
use Sfcms\JqGrid\Provider;

/**
 * Модель журнала моделей
 * @author: keltanas
 * @link http://siteforever.ru
 */ 
class LogModel extends Model
{
    public function relation()
    {
        return array(
            'User' => array( self::BELONGS, 'User', 'user' ),
        );
    }

    public function getProvider()
    {
        $provider = new Provider( $this->app() );
        $provider->setModel( $this );

        $criteria = $this->createCriteria();
        $criteria->order = '`id` DESC';

        $provider->setCriteria( $criteria );

        $provider->setFields(
            array(
                'id'    => array(
                    'title' => t('log','Id'),
                    'width' => 10,
                    'search' => false,
                ),
                'user'    => array(
                    'title' => t('log','User'),
                    'width' => 50,
                    'search' => false,
                    'value' => 'User.login',
                ),
                'email'    => array(
                    'title' => t('log','Email'),
                    'width' => 50,
                    'search' => false,
                    'value' => 'User.email',
                ),
                'object'    => array(
                    'title' => t('log','Object'),
                    'width' => 50,
                    'search' => false,
                ),
                'timestamp'    => array(
                    'title' => t('log','Time'),
                    'width' => 50,
                    'search' => false,
                    'format' => array(
                        'timestamp' => array( 'format' => '%d-%m-%Y %H:%M' ),
                    ),
                ),
            )
        );

        return $provider;
    }
}
