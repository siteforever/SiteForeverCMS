<?php
/**
 * Контроллер страниц
 * @author keltanas <nikolay@ermin.ru>
 */
namespace Module\Page\Controller;

use Module\Page\Form\PageForm;
use Sfcms\Controller;
use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Sfcms\Form\Exception\ValidationException;
use Sfcms\Form\Form;
use Sfcms_Http_Exception;
use Sfcms\Request;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PageController extends Controller
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

    public function init()
    {
        if ($this->page){
            $this->request->setTitle($this->page->title);
            $this->tpl->getBreadcrumbs()->fromSerialize($this->page->get('path'));
        }
    }

    /**
     * @return mixed
     * @throws Sfcms_Http_Exception
     */
    public function indexAction()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');

        if ('page' != $this->page->controller) {
            throw new NotFoundHttpException(sprintf('"Page" has not page controller [%s]', $this->page->controller));
        }

        if (!$this->auth->hasPermission($this->page['protected'])) {
            throw new HttpException(403, $this->t('Access denied'));
        }

        if ($this->page && $this->request->get('alias')) {
            throw new HttpException(404, $this->t('Page not found'));
        }

        $this->app->getLogger()->debug(__METHOD__, [$this->page->attributes]);

        // создаем замыкание страниц (если одна страница указывает на другую)
        $countDown = 3;
        while ($this->page['link'] && 'page' == $this->page->controller && $countDown) {
            if ($this->page->id == $this->page->link) {
                throw new BadRequestHttpException('Page id equals link');
            }
            $page = $pageModel->find($this->page['link']);
            if (!$this->auth->hasPermission($page['protected'])) {
                throw new HttpException(403, $this->t('Access denied'));
            }
            if (!$page['link']) {
                return $this->redirect($page['alias']);
            }
            $this->page = $page;
            $this->app->getLogger()->debug(__METHOD__, [$this->page->attributes]);
            $countDown--;
        }

        if ('page' == $this->page->controller && $this->page['id'] != 1) {
            $subpages = $pageModel->parents[$this->page['id']];
            if (count($subpages)) {
                $this->tpl->assign('subpages', $subpages);
                $this->tpl->assign('page', $this->page);
            }
        }

        return $this->render('page.index', array('content' => $this->page->content));
    }

    public function protectedAction()
    {
        $this->request->setTitle($this->t('Access denied'));
    }

    /**
     * Структура
     * @return mixed
     */
    public function adminAction()
    {
        // используем шаблон админки
        $this->request->set('template', 'index');
        $this->request->setTitle($this->t('Site structure'));

        /** @var PageModel $model */
        $model = $this->getModel('Page');

        if ($get_link_add = $this->request->get('get_link_add')) {
            return $this->render('get_link_add', array('id' => $get_link_add));
        }

        return $this->render('page.admin', array(
            'data' => $model->getParents(),
        ));
    }


    /**
     * Создает страницу
     * @param int $id
     * @return mixed
     */
    public function createAction($id)
    {
        /** @var $model PageModel */
        $model = $this->getModel('Page');
        $modules = $this->get('module.manager')->getAvailableModules();

        if (null === $id) {
            return $this->t('Unknown error');
        }

        $parent = $model->find($id);
        return array(
            'parent' => $parent,
            'modules' => $modules,
        );
    }


    /**
     * Additional new page
     * @return mixed
     */
    public function addAction()
    {
        /**
         * @var PageModel $model
         */
        $model = $this->getModel('Page');
        $parent = $this->request->request->getDigits('parent', null);

        // идентификатор раздела, в который надо добавить
        $name = $this->request->request->get('name');
        $module = $this->request->request->get('module');

        // родительский раздел
        /** @var $parentObj Page */
        $parentObj = null;
        if (null !== $parent) {
            $parentObj = $model->find($parent);
        }

        /** @var $form Form */
        $form = $this->get('page.form.edit');

        $form->setData(array(
            'parent'    => $parent,
            'template'  => 'inner',
            'author'    => $this->user->id,
            'content'   => '<p>' . $this->t('Home page for the filling') . '',
            'date'      => time(),
            'update'    => time(),
            'pos'       => $model->getNextPos($parent),
        ));

        $alias = $this->i18n->translit(trim($name, ' /'));
        if ($parentObj && $parentObj->alias && 'index' != $parentObj->alias) {
            $alias = trim($parentObj->alias, ' /') . '/' . $alias;
        }
        $form->getChild('alias')->setValue($alias);

        if ($parentObj && $parentObj->sort) {
            $form->getChild('sort')->setValue($parentObj->sort);
        }

        $form->getChild('action')->setValue('index');
        $form->getChild('name')->setValue($name);
        $form->getChild('controller')->setValue($module);

        $this->request->setTitle($this->t('Create page'));
        $this->tpl->assign('form', $form);
        return $this->tpl->fetch('page.edit');
    }


    /**
     * @param int $edit идентификатор раздела, который надо редактировать
     * @return mixed
     */
    public function editAction($edit)
    {
        /** @var PageModel $model */
        $model = $this->getModel('Page');
        $form  = $this->get('page.form.edit');

        if ($edit) {
            // данные страницы
            $page = $model->find($edit);
            if ($page) {
                $form->setData($page->getAttributes());

                return array('form' => $form);
            }
        }

        return $this->t('Data not valid');
    }


    /**
     * Сохраняет данные формы
     * @return string
     */
    public function saveAction()
    {
        /** @var PageModel $model */
        $model = $this->getModel('Page');

        /** @var PageForm $form */
        $form = $this->get('page.form.edit');

        if ($form->handleRequest($this->request)) {
            $this->app->getLogger()->debug(__FUNCTION__, $form->getData());
            if ($form->validate()) {
                /** @var $obj Page */
                if ( $id = $form->getChild('id')->getValue() ) {
                    $obj = $model->find( $id );
                    $obj->attributes = $form->getData();
                    $obj->update = time();
                } else {
                    $obj = $model->createObject();
                    $obj->attributes = $form->getData();
                    $obj->update = time();
                    $obj->markNew();
                }
                return ['error'=>0,'msg'=>$this->t( 'Data save successfully' )];
            } else {
                $this->app->getLogger()->error('Page Save Validate', $form->getErrors());
                throw new ValidationException($form->getErrors());
            }
        }
        return array( 'error' => 1, 'msg' => $this->t('Unknown error') );
    }


    /**
     * Удаление страницы
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $page = $this->getModel('Page')->find($id);
        $page->deleted = 1;

        if (!$this->request->isAjax()) {
            return $this->reload('page/admin');
        }

        return $this->renderJson(array('error' => 0, 'msg' => 'ok', 'id' => $id));
    }

    /**
     * Resort pages
     * @return mixed
     */
    public function resortAction()
    {
        $sort = $this->request->get('sort');
        if ($sort) {
            return $this->getModel('Page')->resort($sort) ? 'done' : 'fail';
        }
        return $this->t('Unknown error');
    }


    /**
     * Меняет св-во hidden у страницы
     *
     * @param int $id
     *
     * @return array
     */
    public function hiddenAction($id)
    {
        if ($id) {
            $page = $this->getModel('Page')->find($id);
            $page->hidden = intval(!$page->hidden);
            return array('page' => $page);
        } else {
            return array('error' => 1);
        }
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
        foreach ($pages as $page) {
            try {
                $page->save();
                $return[] = 'Алиас &laquo;' . $page->name . '&raquo; &rarr; &laquo;' . $page->alias . '&raquo; пересчитан';
            } catch (Exception $e) {
                $return[] = 'Алиас &laquo;' . $page->name . '&raquo; &rarr; ' . $e->getMessage(
                    ) . ' &laquo;' . $page->alias . '&raquo;';
            }
        }

        return '<p>Пересчет алиасов завершен</p>' . join('<br>', $return);
    }

}
