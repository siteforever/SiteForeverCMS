<?php
namespace Sfcms\Form;

/**
 * Класс формы
 * @author keltanas
 */
class Form extends FormBaseAbstract
{
    /** @var FormView */
    private $view;

    /**
     * @param $name
     * @deprecated
     *
     * @return string
     */
    public function htmlFieldWrapped($name)
    {
        if (null === $this->view) {
            $this->view = $this->createView();
        }
        return $this->view->htmlFieldWrapped($name);
    }

    /**
     * @param $name
     * @deprecated
     *
     * @return string
     */
    public function htmlField($name)
    {
        if (null === $this->view) {
            $this->view = $this->createView();
        }
        return $this->view->htmlField($name);
    }
}
