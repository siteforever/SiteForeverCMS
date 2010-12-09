<?php
/**
 *
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class controller_feedback extends Controller
{
    function indexAction()
    {
        $form = new form_Form(array(
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
}
