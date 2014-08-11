<?php
/**
 * Controller обратной связи
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\Feedback\Controller;

use Sfcms;
use Sfcms\Controller;
use Module\Feedback\Form\DefaultForm;

class FeedbackController extends Controller
{
    public function indexAction()
    {
        $this->request->setTitle($this->t('Feedback'));
        $this->request->setTemplate('inner');
        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index', $this->t('Home'))
            ->addPiece(null, $this->request->getTitle());

        /** @var $form DefaultForm */
        $form = new DefaultForm();

        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $location = $this->container->getParameter('root') . '/files/attachment';
                /** @var Sfcms\Form\Field\File $fileFiled */
                $fileFiled = $form->getChild('image');
                $targetFile = $fileFiled->moveTo($location);

                $message = $this->createMessage(
                    $form->email,
                    $this->config->get('admin'),
                    $this->t('Message from site') . ' :' . $form->title,
                    $form->message
                );
                $message->attach(new \Swift_Attachment($targetFile->getPathname()));
                $this->sendMessage($message);

                $form->message->clear();
                $form->title->clear();
                $this->request->addFeedback('Your message was sent');
            }
        }

        $this->tpl->assign('form', $form);
        return $this->tpl->fetch('feedback.form');
    }

    public function adminAction()
    {

    }
}
