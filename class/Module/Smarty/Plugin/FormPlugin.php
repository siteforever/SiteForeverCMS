<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Smarty\Plugin;

use Sfcms\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormPlugin
{
    protected function resolveParams($params)
    {
        $class = array();
        if (isset($params['class'])) {
            $class = (array) $params['class'];
        }
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
                'form'
            ));
        $resolver->setDefaults(array(
                'hint' => true,
                'buttons' => true,
                'domain' => 'messages',
                'class' => '',
                'action' => '',
                'method' => '',
            ));
        $params = $resolver->resolve($params);
        $params['class'] = join(' ', $class + (array) $params['class']);
        return $params;
    }

    public function block_form($params, $content, \Smarty_Internal_Template $smarty, &$repeat)
    {
        if ($content) {
            $params = $this->resolveParams($params);
            $params['form'] = $params['form']->createView();
            $params['content'] = $content;
            /** @var \Smarty_Internal_Template $template */
            $template = $smarty->smarty->createTemplate('smarty/function_form.tpl');
            $template->assign($params);
            return $template->fetch('smarty/form/block.tpl');
        }
        return '';
    }
}