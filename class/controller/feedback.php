<?php
/**
 * Controller обратной связи
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Controller_Feedback extends Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setTitle('Обратная связь');
        $this->request->setTemplate('inner');
        $bc = $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',t('Home'))->addPiece(null,$this->request->getTitle());

        /** @var $form Forms_Feedback_Default */
        $form = $this->getForm( 'feedback_default' );

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                sendmail(
                   $form->name.' <'.$form->email.'>',
                   $this->config->get('admin'),
                   'Сообщение с сайта :'.$form->title,
                   $form->message
                );
                $form->message->clear();
                $form->title->clear();
                $this->request->addFeedback('Ваше сообщение отправлено');
            } else {
                $this->request->addFeedback($form->getFeedbackString());
            }
        }

        $this->tpl->assign('form', $form);
        return $this->tpl->fetch('feedback.form');
    }

    public function adminAction()
    {

    }
}
