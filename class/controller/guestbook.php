<?php
/**
 * Сообщения гостевой книги
 * @author: keltanas <keltanas@gmail.com>
 */
class Controller_Guestbook extends Sfcms_Controller
{
    public function init()
    {
        parent::init();

        $this->request->setTitle( t('Guestbook module') );
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
        $this->request->setTitle( $this->page['title'] ? $this->page['title'] : $this->page['name'] );

        $this->app()->getLogger()->log( $this->page );

        $link   = $this->page['id'];

        $model  = $this->getModel('Guestbook');

        $form       = new Forms_Guestbook_Form();

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $this->app()->getLogger()->log( $form->getData() );

                $obj = $model->createObject();
                $obj->set( 'name', strip_tags( $form->getField( 'name' )->getValue() ) );
                $obj->set( 'email', strip_tags( $form->getField( 'email' )->getValue() ) );
                $obj->set( 'message', strip_tags( $form->getField( 'message' )->getValue() ) );
                $obj->set( 'link', $link );
                $obj->set( 'date', time() );
                $obj->set( 'ip', $_SERVER['REMOTE_ADDR'] );

                $model->save( $obj );
            }
        }

        $crit   = array(
            'cond'  => ' link = ? ',
            'params'=> array( $link ),
        );

        $count  = $model->count( $crit['cond'], $crit['params'] );

        $paging = $this->paging( $count, 10, $this->page['alias'] );

        $crit['order'] = ' date DESC ';
        $crit['limit'] = $paging->limit;

        $this->app()->getLogger()->log( $crit );

        $messages   = $model->findAll( $crit );

//        $this->app()->getLogger()->log( $messages->current()->message );

        $this->tpl->assign(array(
            'messages'  => $messages,
            'paging'    => $paging,
            'form'      => $form,
        ));

        $this->request->setContent( $this->tpl->fetch('guestbook.index') );
    }


    /**
     * Админка
     */
    public function adminAction()
    {
        $this->request->addScript('/misc/admin/guestbook.js');
        $id   = $this->request->get('id');

        if ( ! $id ) {
            $this->request->setContent('Param Id not defined');
            return;
        }

        $crit   = array(
            'cond'  => ' link = ? ',
            'params'=> array( $id ),
        );

        $model  = $this->getModel('Guestbook');

        $count  = $model->count( $crit['cond'], $crit['params'] );

        $paging = $this->paging( $count, 10, $this->page['alias'] );

        $crit['order'] = ' date DESC ';
        $crit['limit'] = $paging->limit;

        $this->app()->getLogger()->log( $crit );

        $messages   = $model->findAll( $crit );

        $this->tpl->assign(array(
            'messages'  => $messages,
            'paging'    => $paging,
        ));

        $this->request->setContent( $this->tpl->fetch('system:guestbook.admin') );
    }


    /**
     * Редактирование сообщения
     */
    public function editAction()
    {
        $this->request->getTitle( t('Edit') );

        $id = $this->request->get('id');

        $model  = $this->getModel('Guestbook');

        $msg    = $model->find( $id );

        $form   = new Forms_Guestbook_Edit();
        if ( $form->getPost() ) {
            if ( $form->validate() ) {

            }
        } else {
            $form->setData( $msg );
        }

        $this->tpl->assign( array(
            'msg'   => $msg,
            'form'  => $form,
        ) );
        $this->request->setContent( $this->tpl->fetch('guestbook.edit') );
    }

}
