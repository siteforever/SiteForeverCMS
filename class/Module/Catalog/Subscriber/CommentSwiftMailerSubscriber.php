<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Subscriber;


use Module\Catalog\Object\Comment;
use Sfcms\Model\ModelEvent;
use Sfcms\Tpl\Driver;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentSwiftMailerSubscriber extends ContainerAware implements EventSubscriberInterface
{
    /** @var string */
    private $email;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'comment.save.start' => 'onCommentSave',
        );
    }

    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->container->get('mailer');
    }

    /**
     * @return Driver
     */
    public function getTpl()
    {
        return $this->container->get('tpl');
    }

    public function onCommentSave(ModelEvent $event)
    {
        /** @var Comment $object */
        $object = $event->getObject();
        if ($object->getId()) {
            return;
        }

        if (!$this->email) {
            return;
        }

        $this->getTpl()->assign(array(
            'object' => $object,
        ));
        $messageBody = $this->getTpl()->fetch('mail.catalog_comment_create');

        /** @var \Swift_Message $message */
        $message = $this->getMailer()->createMessage();
        $message
            ->setSubject('New comment')
            ->setFrom($this->email)
            ->setTo($this->email)
            ->setBody($messageBody, 'text/html', 'utf-8')
        ;
        $this->getMailer()->send($message);
    }
}
