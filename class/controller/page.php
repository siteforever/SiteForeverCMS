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
            'system'    => array('admin','edit','add','correct','move','nameconvert','save','realias',),
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

    /**
     * Ошибка 404
     * @return void
     */
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
            return $this->correctAction();
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


        $this->request->setTitle( 'Структура сайта' );

        $sort   = $this->request->get( 'sort' );
        if ( $sort ) {
            return  $model->resort( $sort );
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

        $this->tpl->html    = join( "\n", $model->html );
        $this->request->setContent( $this->tpl->fetch('system:page.admin') );
    }

    /**
     * @return void
     */
    public function nameconvertAction()
    {
        $this->request->setTemplate( 'inner' );
        $this->request->setContent( __METHOD__ );
    }

    /**
     * Добавления
     * @return void
     */
    function addAction()
    {
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

        $form  = $model->getForm();

        $form->parent      = $parent_id;
        $form->template    = 'inner';

        if ( isset($parent['alias']) ) {
            $form->alias   = $parent['alias'];
        }

        $form->author  = '1';
        $form->content = t('Home page for the filling');

        $form->date    = time();
        $form->update  = time();

        if ( isset($parent['controller']) ) {
            $form->controller  = $parent['controller'];
        }

        if ( isset($parent['action']) ) {
            $form->action      = $parent['action'];
        }

        $form->pos     = $model->getNextPos($parent_id);

        if ( isset($parent['sort']) ) {
            $form->sort    = $parent['sort'];
        }

        $this->request->setTitle( 'Добавить страницу' );
        $this->tpl->form    = $form;
        $this->request->setContent($this->tpl->fetch('system:page.edit'));
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        /**
         * @var Model_Page $model
         */
        $model  = $this->getModel('Page');

        $form   = $model->getForm();

        if ( $form->getPost() )
        {
            if ( $form->validate() )
            {
                $form->update   = time();
                $obj    = $model->createObject( $form->getData() );

                // Если с таким маршрутом уже есть страница, то не сохранять
                if ( $page = $model->findByRoute( $obj->alias ) )
                {
                    if ( $page->id != $obj->getId() ) {
                        $this->request->addFeedback(t('The page with this address already exists'));
                        $this->request->addFeedback(t('Data not saved'));
                        $obj->markClean();
                        return;
                    }

                    if ( ! $obj->getId() ) {
                        $this->request->addFeedback(t('The page with this address already exists'));
                        return;
                    }
                }

                $old_id = $obj->getId();

                try {
                    if ( $obj->save() ) {
                        $this->request->addFeedback(t('Data save successfully'));
                        if ( ! $old_id ) {
                            reload(null, array('controller'=>'page','action'=>'edit','edit'=>$obj->getId()));
                        }
                    } else {
                        $this->request->addFeedback(t('Data not saved'));
                    }
                } catch ( ModelException $e ) {
                    $this->request->addFeedback( $e->getMessage() );
                }
            } else {
                $this->request->addFeedback( $form->getFeedback() );
            }
        }
    }

    /**
     * @return void
     */
    public function editAction()
    {
        /**
         * @var Model_Page $model
         */
        $model  = $this->getModel('Page');

        $form   = $model->getForm();

        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle('Управление сайтом');

        // идентификатор раздела, который надо редактировать
        $edit_id = $this->request->get('edit', FILTER_SANITIZE_NUMBER_INT);

        // идентификатор раздела, в который надо добавить
        $add_id  = $this->request->get('add', FILTER_SANITIZE_NUMBER_INT);

        // родительский раздел
        if( $add_id ) {
            $parent     = $model->find( $add_id );
        }

        if ( $edit_id ) {
            // данные раздела
            $part = $model->find( $edit_id );

            if ( $part ) {
                $form->setData( $part->getAttributes() );
            }

            $this->tpl->form    = $form;
            $this->request->setContent($this->tpl->fetch('system:page.edit'));
        }
        else {
            $this->request->setContent(t('Data not valid'));
        }
    }

    /**
     * Перемещение раздела
     * @return void
     */
    public function moveAction()
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

    /**
     * Пересчитает все алиасы структуры
     * @return void
     */
    public function realiasAction()
    {
        $this->request->setTitle('Пересчет алиасов');
        $pages  = $this->getModel('Page')->findAll(array('cond'=>'deleted = 0'));
        ob_implicit_flush(1);

        /**
         * @var Data_Object_Page $page
         */
        foreach ( $pages as $page ) {
            try {
                $page->save();
                print('Алиас &laquo;' . $page->alias .'&raquo; пересчитан<br />');
                //$this->request->addFeedback('Алиас &laquo;' . $page->alias .'&raquo; пересчитан');
            } catch ( Exception $e ) {
                print( $e->getMessage() . ' &laquo;' . $page->alias .'&raquo;<br />' );
                //$this->request->addFeedback( $e->getMessage() . ' &laquo;' . $page->alias .'&raquo;' );
            }
        }

        $this->request->setContent('Пересчет алиасов завершен');
    }

}
