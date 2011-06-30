<?php
/**
 * Контроллер страниц
 * @author keltanas <nikolay@ermin.ru>
 */

class Controller_Page extends Controller
{

    function access()
    {
        return array(
            'system'    => array('admin','edit','add','move'),
        );
    }

    /**
     * @return
     */
    public function indexAction()
    {
        if ( ! $this->user->hasPermission( $this->page['protected'] ) )
        {
            $this->request->setContent(t('Access denied'));
            return;
        }
        
        // создаем замыкание страниц
        while ( $this->page['link'] != 0 )
        {
            $page = $this->getModel('Page')->find( $this->page['link'] );

            if ( ! $this->user->hasPermission( $page['protected'] ) ) {
                $this->request->setContent(t('Access denied'));
                return;
            }
            $this->page['content']  = $page['content'];
            $this->page['link']     = $page['link'];
        }
    }

    public function errorAction()
    {
        $this->request->set('template', 'inner');
        $this->request->setTitle('Ошибка 404. Страница не найдена');
        $this->request->setContent('Ошибка 404.<br />Страница не найдена.');
    }



    /**
     * Структура
     * @return void
     */
    function adminAction()
    {
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle('Управление сайтом');

        /**
         * @var Model_Page $model
         */
        $model  = $this->getModel('Page');

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

        $sort   = $this->request->get('sort');
        if ( $sort ) {
            return $model->resort( $sort );
        }

        $do     = $this->request->get( 'do' );
        $part   = $this->request->get( 'part' );
        if ( $do && $part ) {
            $model->switching( $do, $part );
            redirect('admin');
        }

        $model->createTree();

        //printVar($model->parents);
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
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle('Управление сайтом');

        /**
         * @var Model_Page $model
         */
        $model  = $this->getModel('Page');

        // идентификатор раздела, в который надо добавить
        $parent_id  = $this->request->get('add');

        // родительский раздел
        if( $parent_id ) {
            $parent     = $model->find( $parent_id );
        }
        else {
            $parent     = $model->createObject( array(
                'controller'    => 'page',
                'action'        => 'index',
                'sort'          => 'pos',
            ));
        }

        $edit_form  = $model->getForm();

        if ( $edit_form->getPost() )
        {
            if ( $edit_form->validate() )
            {
                $form_data  = $edit_form->getData();

                $found_page = $model->find(array(
                    'cond'      => 'alias = :alias',
                    'params'    => array(':alias'=>$edit_form->alias,),
                ));

                if ( $found_page ) {
                    $this->request->addFeedback(t('The page with this address already exists'));
                    return;
                }

                if ( $model->save( $model->createObject( $form_data ) ) )
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


        $edit_form->parent      = $parent_id;
        $edit_form->template    = 'inner';

        if ( isset($parent['alias']) ) {
            $edit_form->alias   = $parent['alias'];
        }

        $edit_form->author  = '1';
        $edit_form->content = t('Home page for the filling');

        $edit_form->date    = time();
        $edit_form->update  = time();

        if ( isset($parent['controller']) ) {
            $edit_form->controller  = $parent['controller'];
        }
        if ( isset($parent['action']) ) {
            $edit_form->action      = $parent['action'];
        }
        $next_pos   = $model->getNextPos($parent_id);
        $edit_form->pos     = $next_pos;

        if ( isset($parent['sort']) ) {
            $edit_form->sort    = $parent['sort'];
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
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle('Управление сайтом');

        /**
         * @var Model_Page $model
         */
        $model  = $this->getModel('Page');

        // идентификатор раздела, который надо редактировать
        $part_id = $this->request->get('edit');

        $edit_form = $model->getForm();

        if ( $edit_form->getPost() )
        {
            $edit_form->getField('update')->setValue(time());

            if ( $edit_form->validate() )
            {
                try {
                    $obj    = $model->createObject( $edit_form->getData() );

                    // Если с таким маршрутом уже есть страница, то не сохранять
                    if ( $page = $model->findByRoute( $obj->alias ) ) {
                        if ( $page->id != $obj->getId() ) {
                            $this->request->addFeedback(t('The page with this address already exists'));
                            $this->request->addFeedback(t('Data not saved'));
                            $obj->markClean();
                            return;
                        }
                    }

                    // Если новая запись, то надо узнать id
                    if ( ! $obj->getId() ) {
                        $model->save( $obj );
                    }

                    // Обновляем путь
                    $obj->path = $model->findPathJSON( $obj->getId() );

                    if ( $model->save( $obj ) ) {
                        $this->request->addFeedback(t('Data save successfully'));
                    } else {
                        $this->request->addFeedback(t('Data not saved'));
                    }
                    return;
                } catch ( Exception $e ) {
                    $this->request->addFeedback($e->getMessage());
                }
            } else {
                $this->request->addFeedback($edit_form->getFeedbackString());
            }
            return;
        }

        // данные раздела
        $part = $model->find( $part_id );

        if ( $part ) {
            $edit_form->setData( $part->getAttributes() );
        }

        $this->request->setContent($edit_form->html());
    }

    /**
     * Перемещение раздела
     * @return void
     */
    function moveAction()
    {
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle('Управление сайтом');

        $model  = $this->getModel('Page');
        if ( $up = $this->request->get('up') ) {
            $model->moveUp( $up );
        }
        if ( $down = $this->request->get('down') ) {
            $model->moveDown( $down );
        }
        reload('admin');
    }

}
