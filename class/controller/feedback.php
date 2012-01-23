<?php
/**
 * Rjynhjkkth обратной связи
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Controller_Feedback extends Sfcms_Controller
{
    function indexAction()
    {
        $this->request->setTitle('Контакты');
        $this->request->setTemplate('inner');
        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', 'Главная');
        $bc->addPiece('feedback', $this->request->getTitle());

        $form = $this->getForm();

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
            }
            else {
                $this->request->addFeedback($form->getFeedbackString());
            }
        }

        $this->tpl->form    = $form;
        $this->request->setContent($this->tpl->fetch('feedback.form'));
    }

    function adminAction()
    {

    }

    function getForm()
    {
        return new form_Form(array(
                    'name'      => 'feedback',
                    'fields'    => array(
                        'name'      => array(
                            'type'      => 'text',
                            'label'     => 'Ваше имя',
                            'required',
                        ),
                        'email'     => array(
                            'type'      => 'text',
                            'label'     => 'Email',
                            'required',
                        ),
                        'title'     => array(
                            'type'      => 'text',
                            'label'     => 'Тема',
                        ),
                        'message'   => array(
                            'type'      => 'textarea',
                            'label'     => 'Сообщение'
                        ),
                        'submit'    => array(
                            'type'      => 'submit',
                            'value'     => 'Отправить',
                        ),
                    ),
                ));
    }
}
