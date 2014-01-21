<?php
/**
 * This file is part of the SiteForever package.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Form;

use Symfony\Component\Form\FormView as SymfonyFormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormView extends SymfonyFormView
{
    public function __toString()
    {
        return (string) $this->html();
    }

    public function html($options = array())
    {
        if (2 == func_num_args()) {
            $options = array(
                'hint' => func_get_arg(0),
                'buttons' => func_get_arg(1),
            );
        }
        if (is_bool($options)) {
            $options = array(
                'hint' => func_get_arg(0),
            );
        }
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
                'hint' => true,
                'buttons' => true,
                'domain' => 'messages',
                'class' => '',
            ));

        $tpl = \App::cms()->getTpl();
        $tpl->assign('form', $this);
        $tpl->assign($resolver->resolve($options));
        return $tpl->fetch('smarty.form.html');
    }

    public function htmlFieldWrapped($name)
    {
        if (isset($this[$name])) {
            /** @var FormView $child */
            return $this[$name]->html();
        }
        return sprintf('View for field "%s" not found', $name);
    }
    public function htmlField($name)
    {
        if (isset($this[$name])) {
            /** @var FormView $child */
            $tpl = \App::cms()->getTpl();
            $tpl->assign('form', $this[$name]);
            return $tpl->fetch('smarty.form.field');
        }
        return sprintf('View for field "%s" not found', $name);
    }
}
