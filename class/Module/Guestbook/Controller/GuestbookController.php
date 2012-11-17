<?php
/**
 * Сообщения гостевой книги
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Module\Guestbook\Controller;

use Sfcms_Controller;
use Forms_Guestbook_Form;
use Forms_Guestbook_Edit;

class GuestbookController extends Sfcms_Controller
{
    public function init()
    {
        parent::init();
        $this->request->setTitle( t('guestbook','Guestbook module') );
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
            $this->request->setContent( t('Can not be used without page') );
            return;
        }
        $this->request->setTitle( $this->page->title );

        $link   = $this->page->getId();

        $model  = $this->getModel('Guestbook');

        $form       = new Forms_Guestbook_Form();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $obj = $model->createObject();

                $obj->set( 'name', strip_tags( $form->getField( 'name' )->getValue() ) );
                $obj->set( 'email', strip_tags( $form->getField( 'email' )->getValue() ) );
                $obj->set( 'message', strip_tags( $form->getField( 'message' )->getValue() ) );
                $obj->set( 'link', $link );
                $obj->set( 'date', time() );
                $obj->set( 'ip', $_SERVER['REMOTE_ADDR'] );

                $model->save( $obj );

                sendmail(
                    $obj->email,
                    $this->config->get('admin'),
                    'Сообщение в гостевой '.$this->config->get('sitename').' №'.$obj->getId(),
                    $this->getTpl()->fetch('guestbook.letter')
                );

                sendmail(
                    $obj->email,
                    'keltanas@gmail.com',
                    'Сообщение в гостевой '.$this->config->get('sitename').' №'.$obj->getId(),
                    $this->getTpl()->fetch('guestbook.letter')
                );

            }
        }

        $crit   = array(
            'cond'  => ' link = ? AND hidden != 1 ',
            'params'=> array( $link ),
        );

        $count  = $model->count( $crit['cond'], $crit['params'] );

        $paging = $this->paging( $count, 10, $this->page->alias );

        $crit['order'] = ' date DESC ';
        $crit['limit'] = $paging->limit;

        $messages   = $model->findAll( $crit );

//        $this->log( $messages->current()->message );

        $this->tpl->assign(array(
            'messages'  => $messages,
            'paging'    => $paging,
            'form'      => $form,
        ));

        $this->tpl->fetch('guestbook.index');
    }


    /**
     * Админка
     */
    public function adminAction()
    {
        $this->app()->addScript('/misc/admin/guestbook.js');

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
        $this->request->getTitle( t('Edit') );

        $id = $this->request->get('id');

        $model  = $this->getModel('Guestbook');

        if ( $id ) {
            $msg = $model->find( $id );
        } else {
            $msg = $model->createObject();
        }

        $form   = new Forms_Guestbook_Edit();
        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $msg->setAttributes( $form->getData() );
                $msg->markDirty();
                return array( 'error'=>0, 'msg'=>t('Data save successfully'));
            } else {
                return array( 'error'=>1, 'msg'=>$form->getFeedbackString());
            }
        }

        $form->setData( $msg );

        return array(
            'msg'   => $msg,
            'form'  => $form,
        );
    }
}