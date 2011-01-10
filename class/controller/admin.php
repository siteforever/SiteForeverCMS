<?php
/**
 * Контроллер админки
 * @author keltanas aka Nikolay Ermin
 */

class controller_Admin extends Controller
{

    function init()
    {
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle('Управление сайтом');
    }

    /**
     * Структура
     * @return void
     */
    function indexAction()
    {
        $model  = $this->getModel('structure');

        // добавление
        if ( $this->request->get('add') ) {
            return $this->addAction();
        }

        // правка
        if ( $this->request->get('edit') ) {
            return $this->editAction();
        }

        // обновление
        if ( $this->request->get('up') || $this->request->get('down') ) {
           return $this->moveAction();
        }

        if ( $get_link_add = $this->request->get('get_link_add') ) {
            $this->tpl->id = $get_link_add;
            die($this->tpl->fetch('system:get_link_add'));
        }

        // проверка на правильность алиаса
        if ( $test_alias = $this->request->get('test_alias') ) {

            if (  $model->findByRoute( $test_alias ) ) {
                die('0');
            } else {
                die('yes');
            }
        }


        $this->request->setTitle("Структура сайта");
        
        $do     = $this->request->get( 'do' );
        $part   = $this->request->get( 'part' );

        $sort   = $this->request->get('sort');
        
        if ( $sort ) {
            $sort = array_flip($sort);
            $upd = array();
            foreach( $sort as $id => $pos ) {
                $upd[] = array('id'=>$id, 'pos'=>$pos);
            }
            if ( App::$db->insertUpdateMulti( $model->getTable(), $upd ) )
            {
                die( json_encode( array('errno'=>0, 'error'=>'ok') ) );
            }
            else {
                die( json_encode( array('errno'=>1, 'error'=>'Ошибка сохранения' ) ) );
            }
        }

        // включить
        if ( $do && $part ) {
            $model->switching( $do, $part );
            redirect('admin');
        }

        $model->all = $model->findAll(array(
            'cond'  => 'deleted = 0',
            'order' => 'pos',
        ));
        $model->createTree();

        $model->createHtmlList();

        $this->request->setContent(
            '<div class="b-main-structure">'.join( "\n", $model->html ).'</div>' );
    }

    /**
     * Добавления
     * @return void
     */
    function addAction()
    {
        $model  = $this->getModel('structure');

        // идентификатор раздела, в который надо добавить
        $parent_id  = $this->request->get('add');

        // родительский раздел
        if( $parent_id ) {
            $parent     = $model->find( $parent_id );
        }
        else {
            $parent     = array(
                'controller'    => 'page',
                'action'        => 'index',
                'sort'          => 'pos',
            );
        }

        $edit_form  = $model->getForm();

        if ( $edit_form->getPost() )
        {
            if ( $edit_form->validate() )
            {
                $model->setData( $edit_form->getData() );

                if ( $model->findByRoute( $model->get('alias') ) ) {
                    $this->request->addFeedback(t('The page with this address already exists'));
                    return;
                }

                if ( $model->update() )
                {
                    $this->request->addFeedback(t('Data save successfully'));
                    //reload('admin/edit', array('edit'=>$ins));
                    reload('admin');
                }
                else {
                    $this->request->addFeedback(t('Data not saved'));
                }
            } else {
                $this->request->addFeedback($edit_form->getFeedbackString());
            }
            return;
        }

        $edit_form->parent->setValue( $parent_id );
        $edit_form->template->setValue( 'inner' );

        if ( isset($parent['alias']) ) {
            $edit_form->alias->setValue( $parent['alias'] );
        }
        $edit_form->author->setValue( '1' );

        $edit_form->date->setValue( time() );
        $edit_form->update->setValue( time() );

        if ( isset($parent['controller']) ) {
            $edit_form->controller->setValue( $parent['controller'] );
        }
        if ( isset($parent['action']) ) {
            $edit_form->action->setValue( $parent['action'] );
        }
        $next_pos   = $model->getNextPos($parent_id);
        $edit_form->pos->setValue( $next_pos );

        if ( isset($parent['sort']) ) {
            $edit_form->sort->setValue( $parent['sort'] );
        }

        $this->request->setTitle( 'Добавить страницу' );
        $this->request->setContent( $edit_form->html() );
    }

    /**
     * Редактирование
     * @return void
     */
    function editAction()
    {
        $model  = $this->getModel('structure');

        // идентификатор раздела, который надо редактировать
        $part_id = $this->request->get('edit');

        $edit_form = $model->getForm();

        if ( $edit_form->getPost() )
        {
            $edit_form->update->setValue(time());
            if ( $edit_form->validate() )
            {
                $model->setData( $edit_form->getData() );

                if ( $page = $model->findByRoute( $model->get('alias') ) ) {
                    if ( $page['id'] != $model->get('id') ) {
                        $this->request->addFeedback(t('The page with this address already exists'));
                        $this->request->addFeedback(t('Data not saved'));
                        return;
                    }
                }

                if ( $model->update() )
                {
                    $this->request->addFeedback(t('Data save successfully'));
                }
                else {
                    $this->request->addFeedback(t('Data not saved'));
                }
                return;
            } else {
                $this->request->addFeedback($edit_form->getFeedbackString());
            }
        }
        else {
            // данные раздела
            $part = $model->find( $part_id );

            if ( $part )
            {
                $edit_form->setData( $part );
            }
        }

        $this->request->setContent($edit_form->html());
    }

    /**
     * Перемещение раздела
     * @return void
     */
    function moveAction()
    {
        $model  = $this->getModel('structure');
        if ( $up = $this->request->get('up') ) {
            $model->moveUp( $up );
        }
        if ( $down = $this->request->get('down') ) {
            $model->moveDown( $down );
        }
        reload('admin');
    }


    function listAction()
    {

    }
}
