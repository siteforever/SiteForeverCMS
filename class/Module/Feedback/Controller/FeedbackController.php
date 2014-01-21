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
                $this->sendmail(
                    $form->email,
                    $this->config->get('admin'),
                    $this->t('Message from site') . ' :' . $form->title,
                    $form->message
                );
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
