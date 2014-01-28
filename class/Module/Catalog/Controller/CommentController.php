<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Controller;

use Module\System\Controller\AdminController;
use Sfcms\Controller;
use Sfcms\Form\Form;
use Sfcms\Model;

class CommentController extends AdminController
{
    protected $form = null;

    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array_merge(parent::access(), array(
            USER_ADMIN => array(
                'admin', 'edit', 'delete', 'save',
            ),
        ));
    }

    protected function adminPerPage()
    {
        $config = $this->container->getParameter('catalog');
        return isset($config['comments']['admin']['onPage']) ? $config['comments']['admin']['onPage'] : 10;
    }

    public function getModel($model = '')
    {
        return parent::getModel('CatalogComment');
    }

    protected function adminTitle()
    {
        return 'Comments';
    }

    protected function alias()
    {
        return 'catalogcomment';
    }

    protected function adminFields()
    {
        return array(
            array(
                'label' => 'id',
                'value' => 'id',
                'sort'  => true,
            ),
            array(
                'label' => 'Product',
                'value' => 'Product.name',
                'class' => 'span3',
            ),
            array(
                'label' => 'Name',
                'value' => 'name',
                'sort'  => true,
                'filter' => true,
            ),
            array(
                'label' => 'Email',
                'value' => 'email',
                'sort'  => true,
                'filter' => true,
                'class' => 'span3',
            ),
            array(
                'label' => 'Ip',
                'value' => 'ip',
                'class' => 'span1',
            ),
            array(
                'label' => 'Created At',
                'value' => 'createdAt',
                'sort'  => true,
                'filter' => true,
                'class' => 'span2',
            ),
            array(
                'label' => 'Hidden',
                'value' => 'hidden',
                'bool'  => true,
                'class' => 'span1',
            ),
        );
    }

    /**
     * @param null $name
     *
     * @return Form
     */
    public function getForm($name = null)
    {
        if (null === $this->form) {
            $this->form = new Form(array(
                'name'  => 'comment',
                'class' => 'form-horizontal ajax',
                'action'=> $this->editUrl(),
                'fields'=> array(
                    'id'         => array('type' => 'hidden'),
                    'product_id' => array('type' => 'hidden'),
                    'ip'         => array(
                        'type' => 'text',
                        'label' => 'IP',
                        'required',
                        'filter' => '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',
                    ),
                    'name'       => array('type' => 'text', 'label' => 'You name', 'required',),
                    'email'      => array(
                        'type'   => 'text',
                        'filter' => 'email',
                        'label'  => 'Email',
                        'required',
                    ),
                    'phone'      => array(
                        'type'   => 'text',
                        'filter' => 'phone',
                        'label'  => 'Phone'
                    ),
                    'subject'    => array('type' => 'text', 'label' => 'Subject'),
                    'content'    => array('type' => 'textarea', 'label' => 'Message', 'required',),
                    'createdAt'  => array('type' => 'datetime', 'label' => 'Created At', 'readonly'),
                    'updatedAt'  => array('type' => 'datetime', 'label' => 'Updated At', 'readonly'),
                    'hidden' => array('type'=>'checkbox',
                                      'label'=>$this->t('Hidden'),
                    ),

                    'submit'    => array('type'=>'submit', 'value'=>'Send'),
                ),
            ), $this->request);
        }
        return $this->form;
    }


}
