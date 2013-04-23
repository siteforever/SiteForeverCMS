<?php
/**
 * Контроллер страниц
 * @author keltanas <nikolay@ermin.ru>
 */
namespace Module\Page\Controller;

use Sfcms_Controller;
use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Sfcms\Form\Form;
use Sfcms_Http_Exception;
use Sfcms\Request;

use Exception;

class PageController extends Sfcms_Controller
{
    public function access()
    {
        return array(
            USER_ADMIN    => array(
                'admin',
                'admin2',
                'create',
                'delete',
                'edit',
                'add',
                'correct',
                'move',
                'nameconvert',
                'save',
                'realias',
                'resort',
                'hidden',
            ),
        );
    }

    /**
     * @return mixed
     * @throws Sfcms_Http_Exception
     */
    public function indexAction()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');
        if (!$this->user->hasPermission($this->page['protected'])) {
            throw new Sfcms_Http_Exception(t('Access denied'), 403);
        }

        // создаем замыкание страниц (если одна страница указывает на другую)
        while ($this->page['link']) {
            $page = $pageModel->find($this->page['link']);

            if (!$this->user->hasPermission($page['protected'])) {
                throw new Sfcms_Http_Exception(t('Access denied'), 403);
            }
            $this->page['content'] = $page['content'];
            $this->page['link']    = $page['link'];
        }

        if ('page' == $this->page->controller && $this->page['id'] != 1) {
            $subpages = $pageModel->parents[$this->page['id']];
            if (count($subpages)) {
                $this->tpl->assign('subpages', $subpages);
                $this->tpl->assign('page', $this->page);
            }
        }

        return $this->page->content;
    }

    public function protectedAction()
    {
        $this->request->setTitle(t('Access denied'));
    }

    /**
     * Структура
     * @return mixed
     */
    public function adminAction()
    {
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle(t('Site structure'));

        $this->app()->addScript('/misc/admin/page.js');

        /** @var PageModel $model */
        $model = $this->getModel('Page');

        if ($get_link_add = $this->request->get('get_link_add')) {
            return $this->render('system:get_link_add', array('id' => $get_link_add));
        }

        $model->createParentsIndex();
        return array(
            'data' => $model->parents,
        );
    }


    /**
     * Создает страницу
     * @param int $id
     * @return mixed
     */
    public function createAction($id)
    {
        /** @var $model PageModel */
        $model = $this->getModel( 'Page' );
        $modules = $model->getAvaibleModules();

        if( null === $id ) {
            return t('Unknown error');
        }

        $parent = $model->find( $id );
        return array(
            'parent' => $parent,
            'modules' => $modules,
        );
    }


    /**
     * Добавления
     * @param int $parent
     * @return mixed
     */
    public function addAction($parent)
    {
        /**
         * @var PageModel $model
         */
        $model = $this->getModel( 'Page' );

        // идентификатор раздела, в который надо добавить
        $name      = $this->request->get( 'name' );
        $module    = $this->request->get( 'module' );

        // родительский раздел
        /** @var $parentObj Page */
        $parentObj = null;
        if ( $parent ) {
            $parentObj = $model->find( $parent );
        }

        /** @var $form Form */
        $form = $model->getForm();

        $form->setData(
            array(
                'parent'    => $parent,
                'template'  => 'inner',
                'author'    => $this->app()->getAuth()->getId(),
                'content'   => '<p>'.t( 'Home page for the filling' ).'',
                'date'      => time(),
                'update'    => time(),
                'pos'       => $model->getNextPos( $parent ),
            )
        );

        $alias = $this->i18n()->translit( trim( $name, ' /' ) );
        if ( $parentObj && $parentObj->alias && 'index' != $parentObj->alias ) {
            $alias = trim( $parentObj->alias, ' /' ).'/'.$alias;
        }
        $form->getField('alias')->setValue( $alias );

        if ( $parentObj && $parentObj->sort ) {
            $form->getField('sort')->setValue( $parentObj->sort );
        }

        $form->getField('action')->setValue('index');
        $form->getField('name')->setValue($name);
        $form->getField('controller')->setValue($module);

        $this->request->setTitle( t('Create page') );
        $this->tpl->assign('form', $form);
        return $this->tpl->fetch( 'system:page.edit' );
    }


    /**
     * @param int $edit идентификатор раздела, который надо редактировать
     * @return mixed
     */
    public function editAction( $edit )
    {
        /** @var PageModel $model */
        $model = $this->getModel( 'Page' );
        $form  = $model->getForm();

        if ( $edit ) {
            // данные страницы
            $page = $model->find( $edit );
            if ($page) {
                $form->setData( $page->getAttributes() );
                return array( 'form' => $form );
            }
        }
        return t( 'Data not valid' );
    }


    /**
     * Сохраняет данные формы
     * @return string
     */
    public function saveAction()
    {
        /** @var PageModel $model */
        $model = $this->getModel( 'Page' );

        $form = $model->getForm();

        if ($form->getPost()) {
            if ($form->validate()) {
                /** @var $obj Page */
                if ( $id = $form->getField('id')->getValue() ) {
                    $obj = $model->find( $id );
                    $obj->attributes = $form->getData();
                    $obj->update = time();
                    $obj->markDirty();
                } else {
                    $obj = $model->createObject();
                    $obj->attributes = $form->getData();
                    $obj->update = time();
                    $obj->markNew();
                }
                return array('error'=>0,'msg'=>t( 'Data save successfully' ));
            } else {
                return array('error'=>1,'msg'=>$form->getFeedbackString());
            }
        }
        return array( 'error' => 1, 'msg' => t('Unknown error') );
    }


    /**
     * Удаление страницы
     * @param int $id
     * return array|mixed
     */
    public function deleteAction( $id )
    {
        $page = $this->getModel( 'Page' )->find( $id );
        $page->set('deleted', 1);

        if ( ! $this->request->isAjax() ) {
            $this->reload('page/admin');
        }
        return array('error'=>0,'msg'=>'ok','id'=>$id);
    }

    /**
     * Resort pages
     * @return mixed
     */
    public function resortAction()
    {
        $sort = $this->request->get( 'sort' );
        if ($sort) {
            return $this->getModel( 'Page' )->resort( $sort );
        }
        return t('Unknown error');
    }


    /**
     * @return string
     */
    public function nameconvertAction()
    {
        $this->request->setTemplate( 'inner' );
        return __METHOD__;
    }



    /**
     * Меняет св-во hidden у страницы
     */
    public function hiddenAction( $id )
    {
        $page = $this->getModel( 'Page' )->find( $id );
        $page->hidden = intval( ! $page->hidden );
        $page->save();
        return array( 'page' => $page );
    }


    /**
     * Пересчитает все алиасы структуры
     * @return string
     */
    public function realiasAction()
    {
        $this->request->setTitle( 'Пересчет алиасов' );
        $pages = $this->getModel( 'Page' )->findAll( array( 'cond'=> 'deleted = 0' ) );

        $return = array();

        /** @var Page $page */
        foreach ( $pages as $page ) {
            try {
                $page->save();
                $return[] = 'Алиас &laquo;' . $page->name . '&raquo; &rarr; &laquo;' . $page->alias . '&raquo; пересчитан';
            } catch ( Exception $e ) {
                $return[] = 'Алиас &laquo;' . $page->name . '&raquo; &rarr; ' . $e->getMessage() . ' &laquo;' . $page->alias . '&raquo;';
            }
        }

        return '<p>Пересчет алиасов завершен</p>' . join('<br>', $return);
    }

}
