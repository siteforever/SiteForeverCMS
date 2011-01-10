<?php
/**
 * Контроллер редактирования шаблонов
 * @author keltanas aka Nikolay Ermin
 */

class controller_Templates extends Controller
{

    function init()
    {
        $this->request->set('template', 'index');
        $this->request->set('tpldata.page.name', 'templates');
        $this->request->setTitle('Почтовые шаблоны');
        $this->request->setContent('module Templates');
    }

    function indexAction()
    {
        $list = $this->templates->findAll();

        if ( $this->request->get('edit', FILTER_VALIDATE_INT) ) {
            return $this->editAction();
        }

        $this->tpl->assign('list', $list);
        $this->request->setContent($this->tpl->fetch('templates.index'));
    }

    function editAction()
    {

        $id     = $this->request->get('edit', FILTER_SANITIZE_NUMBER_INT);
        $data   = $this->templates->find( $id );
        $form   = $this->templates->getForm();
        $form->setData( $data );

        //printVar($data);

        if ( $form->getPost() ) {
            if ( $form->validate() ) {
                $this->templates->setData( $form->getData() );
                if ( $this->templates->save() ) {
                    $this->request->addFeedback('Шаблон сохранен успешно');
                }
                else {
                    $this->request->addFeedback('Шаблон не был сохранен');
                }
            } else {
                $this->request->addFeedback($form->getFeedbackString());
            }
            return;
        }

        $this->tpl->assign('form', $form);
        $this->request->setContent($this->tpl->fetch('templates.edit') );
    }

}