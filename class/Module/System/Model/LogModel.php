<?php
namespace Module\System\Model;

use Module\System\Object\Log;
use Module\User\Object\User;
use Sfcms\Model;
use Sfcms\JqGrid\Provider;
use Sfcms\Request;
use RuntimeException;

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
     *
     * @return bool
     * @throws RuntimeException
     */
    public function pluginAllSaveStart( Model\ModelEvent $event )
    {
        // Записываем все события в таблицу log
        try {
            if ($this->config->get('db.log')) {
                $obj = $event->getObject();
                if ($obj instanceof User || $obj instanceof Log) {
                    return false;
                }
                /** @var $log Log */
                $log = $this->createObject();
                $log->user = $this->app()->getAuth()->getId() ?: 0;
                $log->object = get_class($obj);
                $log->action = 'save';
                $log->timestamp = time();
                $log->markNew();
            }
        } catch( RuntimeException $e ) {
            print $e->getMessage();
        }
    }

    public function getProvider(Request $request)
    {
        $provider = new Provider($request);
        $provider->setModel( $this );

        $criteria = $this->createCriteria();
        $criteria->order = '`id` DESC';

        $provider->setCriteria( $criteria );
        $provider->setFields(
            array(
                'id'    => array(
                    'title' => $this->t('log','Id'),
                    'width' => 30,
                    'search' => false,
                ),
                'user'    => array(
                    'title' => $this->t('log','User'),
                    'search' => false,
                    'value' => 'User.login',
                ),
                'email'    => array(
                    'title' => $this->t('log','Email'),
                    'search' => false,
                    'value' => 'User.email',
                ),
                'object'    => array(
                    'title' => $this->t('log','Object'),
                    'search' => false,
                ),
                'timestamp'    => array(
                    'title' => $this->t('log','Time'),
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
