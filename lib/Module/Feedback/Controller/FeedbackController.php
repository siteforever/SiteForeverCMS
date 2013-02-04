<?php
/**
 * Controller обратной связи
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\Feedback\Controller;

use Sfcms;
use Sfcms_Controller;

class FeedbackController extends Sfcms_Controller
{
    public function indexAction()
    {
        if ( null === $this->page ) {
            $this->request->setTemplate('inner');
            $this->request->setTitle('Обратная связь');
            $this->getTpl()->getBreadcrumbs()
                ->addPiece('index',t('Home'))->addPiece(null, $this->request->getTitle());
        }

        /** @var $formPersonal \Forms_Feedback_Personal */
        /** @var $formOrganization \Forms_Feedback_Organization */
//        $formPersonal = $this->getForm( 'Feedback_Default' );
        $formPersonal = $this->getForm( 'Feedback_Personal' );
        $formOrganization = $this->getForm( 'Feedback_Organization' );

        if ( $formPersonal->getPost() ) {
            if ( $formPersonal->validate() ) {
                $this->getTpl()->assign('form',$formPersonal);
                $message = $this->getTpl()->fetch('feedback.email.personal');
                sendmail(
                   $formPersonal->name.' <'.$formPersonal->email.'>',
                   $this->config->get('admin'),
                   'Сообщение с сайта :'.$this->config->get('sitename'),
                    $message
                );
                $formPersonal->getField('message')->clear();
                $this->request->addFeedback('Ваше сообщение отправлено');
                return array('error'=>0,'msg'=>'Ваше сообщение отправлено');
            } else {
                return array('error'=>1,'errors'=>$formPersonal->getErrors());
            }
        }

        if ( $formOrganization->getPost() ) {
            if ( $formOrganization->validate() ) {
                $this->getTpl()->assign('form',$formOrganization);
                $message = $this->getTpl()->fetch('feedback.email.organization');
                sendmail(
                    $formOrganization->name.' <'.$formOrganization->email.'>',
                    $this->config->get('admin'),
                    'Сообщение с сайта :'.$this->config->get('sitename'),
                    $message
                );
                $formPersonal->getField('message')->clear();
                $this->request->addFeedback('Ваше сообщение отправлено');
            } else {
                return array('error'=>1,'errors'=>$formOrganization->getErrors());
            }
        }

        $this->tpl->assign('formPersonal', $formPersonal);
        $this->tpl->assign('formOrg', $formOrganization);
        return $this->tpl->fetch('feedback.form');
    }
}
