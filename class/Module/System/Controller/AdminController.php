<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Controller;

use Sfcms\Controller;
use Sfcms\Data\Object;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AdminController extends Controller
{
    public $dataSeparator = '.';

    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN => array(
                'admin', 'list', 'edit', 'delete', 'save', 'get'
            ),
        );
    }

    /**
     * @return array
     */
    protected abstract function adminFields();

    protected function adminDefaultCriteria()
    {
        $criteria = $this->getModel()->createCriteria();
        $criteria->order = 'id DESC';
        return $criteria;
    }

    protected function adminTitle()
    {
        return $this->getModel()->objectClass();
    }

    protected function adminPerPage()
    {
        return 20;
    }

    protected function alias()
    {
        return preg_replace('/.*?\\\\([^\\\\]+)Controller$/', '\1', get_class($this));
    }

    protected function dataUrl()
    {
        return $this->router->createServiceLink($this->alias(), 'list');
    }

    protected function adminUrl()
    {
        return $this->router->createServiceLink($this->alias(), 'admin');
    }

    protected function editUrl()
    {
        return $this->router->createServiceLink($this->alias(), 'edit');
    }

    /**
     * Create admin list panel
     *
     * @return Response
     */
    public function adminAction()
    {
        $this->request->setTitle($this->adminTitle());

        $criteria = $this->adminDefaultCriteria();
        $count = $this->getModel()->count($criteria);
        $paging = $this->paging($count, $this->adminPerPage(), $this->adminUrl());

        return $this->render('admin.admin', array(
            'fields' => $this->adminFields(),
            'title' => $this->adminTitle(),
            'dataSeparator' => $this->dataSeparator,
            'dataUrl' => $this->dataUrl(),
            'paging' => $paging,
            'filtered' => array_reduce($this->adminFields(), function($result, $item){
                    return $result || (isset($item['filter']) ? $item['filter'] : false);
                }, false),
        ));
    }

    /**
     * Get admin data list
     */
    public function listAction()
    {
        if ($this->request->query->has('id')) {
            $id = $this->request->query->getInt('id');
            if ($this->request->headers->has('x-http-method-override')) {
                switch ($this->request->headers->get('x-http-method-override')) {
                    case 'DELETE':
                        return $this->deleteAction($id);
                    case 'PUT':
                        return $this->editAction($id);
                }
            } else {
                return $this->editAction($id);
            }
        }

        if ($this->request->isMethod('POST')) {
            return $this->editAction($this->request->query->getDigits('id', null));
        }

        $criteria = $this->adminDefaultCriteria();

        if ($this->request->query->has('o')) {
            $criteria->order = $this->request->query->get('o');
            if ($this->request->query->has('dir')) {
                $criteria->order = $criteria->order . ' ' . $this->request->query->get('dir', 'asc');
            }
        }

        $where = array('deleted = 0');
        $params = array();

        if ($this->request->query->has('filter')) {
            $filter = $this->request->query->get('filter');
            foreach ($filter as $key => $val) {
                $where[] = "`$key` LIKE :$key";
                $params[':' . $key] = '%' . $val . '%';
            }
        }

        $criteria->condition = join(' AND ', $where);
        $criteria->params = $params;

        $count = $this->getModel()->count($criteria);
        $paging = $this->paging($count, $this->adminPerPage(), $this->adminUrl());
        $criteria->limit = $paging->limit;
        $criteria->from = $paging->from;

        $listItems = $this->getModel()->findAll($criteria);

        $fields = $this->adminFields();
        $self = $this;
        return $this->renderJson(
            array_map(function(Object $obj) use ($fields, $self, $paging) {
                return array_reduce($fields, function($result, $field) use ($obj, $self, $paging) {
                    $val = $obj->$field['value'];

                    if (false !== strpos($field['value'], $self->dataSeparator)) {
                        $valueKeys = explode($self->dataSeparator, $field['value']);
                        $tmpVal = $obj;
                        foreach ($valueKeys as $key) {
                            if (!is_object($tmpVal)) {
                                break;
                            }
                            $tmpVal = $tmpVal->$key;
                        }
                        $val = $tmpVal;
                    }

                    if (is_object($val)) {
                        if ($val instanceof \DateTime) {
                            $val = $val->format('d.m.Y H:i');
                        }
                    }

                    $result[str_replace($self->dataSeparator, '_', $field['value'])] = $val;
                    $result['_p'] = $paging->pages;
                    return $result;
                }, array());
            }, iterator_to_array($listItems))
        );
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        $entry = $this->getModel()->find($id);
        if (!$entry) {
            return new JsonResponse(array('status' => 'Entry not found'), 404);
        }
        $entry->deleted = true;
        return new JsonResponse(array('status' => 'ok'));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function editAction($id = null)
    {
        if (null !== $id) {
            $entry = $this->getModel()->find($id);
            if (!$entry) {
                return new JsonResponse(array('status' => 'Entry not found'), 404);
            }
        } else {
            $entry = $this->getModel()->createObject();
        }

        $form = $this->getForm();
        if ($this->request->isMethod('PUT') || $this->request->isMethod('POST')) {
            if ($form->handleRequest($this->request)) {
                if ($form->validate()) {
                    if ($form->id) {
                        $entry = $this->getModel()->find($form->id);
                        if (!$entry) {
                            return new JsonResponse(array(
                                'status'=>'fail', 'error' => 'Entry with id '.$form->id.' not found'
                            ), 404);
                        }
                    } else {
                        $entry = $this->getModel()->createObject();
                        $entry->markNew();
                    }
                    $entry->attributes = $form->getData();
                    return new JsonResponse(array(
                        'status' => 'ok',
                        'error' => 0,
                    ));
                } else {
                    return new JsonResponse(array(
                        'status' => 'error',
                        'error'  => 1,
                        'errors' => $form->getErrors()
                    ));
                }
            }
        }

        $form->setData($entry->attributes);
        if (!$form->ip) {
            $form->ip = $this->request->getClientIp();
        }

        return new Response($this->getForm()->html(false, false));
    }
}
