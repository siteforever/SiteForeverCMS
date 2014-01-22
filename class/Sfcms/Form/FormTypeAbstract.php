<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Form;


use Symfony\Component\Form\FormView;

abstract class FormTypeAbstract
{
    /** @var boolean */
    protected $debug = false;
    /** @var FormTypeAbstract  */
    protected $parent   = null;
    /** @var array Child forms and fields */
    protected $children    = array();
    /** @var string */
    protected $name;
    /** @var string */
    protected $class;
    /** @var string */
    protected $id;
    /** @var string */
    protected $type;
    /** @var mixed */
    protected $value;
    /** @var array  */
    protected $options = array();
    /** @var bool element having data */
    protected $datable = true;

    protected $errors = array();

    public abstract function __construct($options);

    public abstract function createView(FormView $parentView = null);

    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Having data
     * @return bool
     */
    protected function isDatable()
    {
        return $this->datable;
    }

    /**
     * Зарегистрировать ошибку от поля
     * @param $field
     * @param $msg
     */
    public function addError($field, $msg)
    {
        $this->errors[$field] = $msg;
    }

    /**
     * Return error list as JSON
     * @return string
     */
    public function getJsonErrors()
    {
        return json_encode($this->errors);
    }

    /**
     * Return all errors as array
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \Sfcms\Form\FormTypeAbstract $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \Sfcms\Form\FormTypeAbstract
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        if ($this->id) {
            return $this->id;
        }
        if ($this->parent) {
            return sprintf('%s_%s', $this->parent->getId(), $this->getName());
        }
        return $this->getName();
    }


    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Вернет список настроек
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param FormTypeAbstract $child
     * @param string $after
     * @return FormTypeAbstract
     */
    public function setChild(FormTypeAbstract $child, $after = null)
    {
        if (!$after) {
            $this->children[$child->getName()] = $child;
            $child->setParent($this);
            return $child;
        }

        $children = $this->children;
        $this->children = array();
        /** @var FormTypeAbstract $child */
        foreach ($children as $key => $child) {
            $this->children[$key] = $child;
            if ($key == $after) {
                $this->children[$child->getName()] = $child;
            }
        }

        return $child;
    }

    /**
     * Вернет поле формы по имени
     * @param $name
     * @return FormFieldAbstract
     * @throws Exception
     */
    public function getChild($name)
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        }
        throw new Exception("Child form named '{$name}' not found");
    }

    /**
     * Очищает значения полей формы
     */
    public function clear()
    {
        if (isset($this->options['empty'])) {
            $this->value = $this->options['empty'];
        } else {
            $this->value = null;
        }

        foreach ($this->children as $child) {
            /** @var $child FormTypeAbstract */
            if (is_object($child)) {
                $child->clear();
            }
        }
    }

    public function getData($toString = false)
    {
        return $toString ? $this->getStringValue() : $this->getValue();
    }

    /**
     * Вернет значение поля
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Вернет значение в виде строки
     * @return string
     */
    public function getStringValue()
    {
        if (is_array($this->value) && count($this->value) == 1) {
            return join('', $this->value);
        }

        return (string) $this->value;
    }

    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     *
     * @param $value
     * @return FormFieldAbstract
     */
    public function setValue($value)
    {
        $value = trim($value);
        $this->value  = $value;
        return $this;
    }

    /**
     * Вернет значение поля формы по имени
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getChild($key)->getValue();
    }

    /**
     * Установит значение полю
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function __set($key, $value)
    {
        try {
            $this->getChild($key)->setValue($value);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
