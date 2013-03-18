<?php
namespace Module\System\Model;

use Module\System\Object\Log;
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

    /**
     * Логировать сохранение объектов
     * @param $event
     */
    public function pluginAllSaveStart( Model\ModelEvent $event )
    {
        $this->log(__METHOD__, $event->getName());
        // Записываем все события в таблицу log
        try {
            if ( $this->config->get('db.log') ) {
                if ( $event->getModel() !== $this ) {
                    $obj = $event->getObject();
                    /** @var $log Log */
                    $log = $this->createObject();
                    $log->user = $this->app()->getAuth()->currentUser()->getId() ?: 0;
                    $log->object = get_class($obj);
                    $log->action = 'save';
                    $log->timestamp = time();
                    $log->save();
                }
            }
        } catch( \Exception $e ) {
            print $e->getMessage();
        }
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
                    'width' => 30,
                    'search' => false,
                ),
                'user'    => array(
                    'title' => t('log','User'),
//                    'width' => 50,
                    'search' => false,
                    'value' => 'User.login',
                ),
                'email'    => array(
                    'title' => t('log','Email'),
//                    'width' => 50,
                    'search' => false,
                    'value' => 'User.email',
                ),
                'object'    => array(
                    'title' => t('log','Object'),
//                    'width' => 50,
                    'search' => false,
                ),
                'timestamp'    => array(
                    'title' => t('log','Time'),
//                    'width' => 50,
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
