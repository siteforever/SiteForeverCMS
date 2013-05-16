<?php
/**
 * @author Keltanas
 */
namespace Sfcms;

use Module\System\Model\SessionModel;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class Session
{
    /** @var SymfonySession  */
    private $_session = null;

    public function __construct(SessionModel $model = null)
    {
        if (defined('TEST') && TEST) {
            $storage = new MockArraySessionStorage();
        } elseif (null !== $model) {
            $storage = new NativeSessionStorage(
                array(),
                new PdoSessionHandler($model->getDB()->getResource(), array('db_table'=>$model->getTable()))
            );
        } else {
            $storage = new NativeSessionStorage();
        }
        $this->_session = new SymfonySession($storage);
    }

    public function session()
    {
        return $this->_session;
    }
}
