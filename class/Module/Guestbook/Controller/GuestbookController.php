<?php
/**
 * Сообщения гостевой книги
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Module\Guestbook\Controller;

use Sfcms\Controller;

class GuestbookController extends Controller
{
    public function init()
    {
        parent::init();
        $this->request->setTitle($this->t('guestbook', 'Guestbook module'));
        if ($this->page){
            $this->request->setTitle($this->page->title);
            $this->tpl->getBreadcrumbs()->fromSerialize($this->page->get('path'));
        }
    }

    public function access()
    {
        return array(
            'system'    => array('admin','edit'),
        );
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        if ( null === $this->page ) {
            return $this->t('Can not be used without page');
        }
        $link  = $this->page->getId();
        $model = $this->getModel('Guestbook');
        $form  = $this->getForm('guestbook.guest');

        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $obj = $model->createObject();

                $obj->set('name', strip_tags($form->getChild('name')->getValue()));
                $obj->set('email', strip_tags($form->getChild('email')->getValue()));
                $obj->set('message', strip_tags($form->getChild('message')->getValue()));
                $obj->set('link', $link);
                $obj->set('date', time());
                $obj->set('ip', $this->request->getClientIp());

                $model->save( $obj );

                $this->tpl->assign('obj', $obj);
                $this->sendmail(
                    $this->container->getParameter('admin'),
                    $this->container->getParameter('guestbook.email'),
                    'Сообщение в гостевой '.$this->container->getParameter('sitename').' №'.$obj->getId(),
                    $this->getTpl()->fetch('guestbook.letter'),
                    'text/html'
                );
            }
            $form->getChild('captcha')->clear();
        }

        $crit   = array(
            'cond'  => ' link = ? AND hidden != 1 ',
            'params'=> array( $link ),
        );

        $count  = $model->count($crit['cond'], $crit['params']);
        $paging = $this->paging($count, 10, $this->page->alias);

        $crit['order'] = ' date DESC ';
        $crit['limit'] = $paging->limit;

        $messages   = $model->findAll($crit);

//        $formBuilder = new

        return $this->render('guestbook.index', array(
            'messages'  => $messages,
            'paging'    => $paging,
            'form'      => $form->createView(),
        ));
    }


    /**
     * Админка
     */
    public function adminAction()
    {
        $model  = $this->getModel('Guestbook');
        $crit = $model->createCriteria();

        if ( $link = $this->request->get('link') ) {
            $crit->condition = ' `link` = ? ';
            $crit->params = array( $link );
        }

        $count  = $model->count( $crit->condition, $crit->params );

        $paging = $this->paging( $count, 20, $this->page['alias'] );

        $crit->order = ' `date` DESC ';
        $crit->limit = $paging->limit;

        $list   = $model->findAll( $crit );

        return array(
            'list'      => $list,
            'paging'    => $paging,
        );
    }


    /**
     * Редактирование сообщения
     */
    public function editAction()
    {
        $this->request->getTitle( $this->t('Edit') );
        $model  = $this->getModel('Guestbook');

        $form = $this->getForm('guestbook.edit');
        if ($form->handleRequest($this->request)) {
            if ($form->validate()) {
                $id = $form->id;
                if (!$id) {
                    return $this->renderJson(array('error'=>1, 'msg'=>$this->t('Id not defined')));
                }
                $message = $model->findByPk($id);
                if (!$message) {
                    return $this->renderJson(array('error'=>1, 'msg'=>$this->t('Message not found')));
                }
                $message->setAttributes($form->getData());
                $message->markDirty();

                return $this->renderJson(array('error' => 0, 'msg' => $this->t('Data save successfully')));
            } else {
                return $this->renderJson(array('error' => 1, 'msg' => $form->getFeedbackString()));
            }
        }

        $id = $this->request->query->getDigits('id', null);
        if ($id) {
            $message = $model->find($id);
        } else {
            $message = $model->createObject();
        }

        $form->setData($message);

        return array(
            'msg'   => $message,
            'form'  => $form,
        );
    }
}
