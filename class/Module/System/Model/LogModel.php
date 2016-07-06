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

    public function getProvider(Request $request)
    {
        $provider = new Provider($request, $this->app()->get('siteforever_cms.pager'));
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
                'object_id'    => array(
                    'title' => $this->t('log','Object id'),
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
