<?php
namespace Sfcms\Form;

/**
 * Базовый класс формы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */
use Sfcms\Form\Field\File;
use Sfcms\Request;
use Symfony\Component\Form\FormView as SymfonyFormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class FormBaseAbstract extends FormTypeAbstract implements \ArrayAccess
{
    /*
     * 1. Получить значения из POST
     * 2. Обработать значения по шаблону, соответственно типам
     * 3. Проверить заполнение обязательных полей
     * 4. Выводить форму в шаблоны
     * 5. Динамически управлять типами и видимостью полей
     * 6. Возвращать данные запроса в виде проверенного массива для создания Domain классов
     */
    /** @var string */
    protected $method;
    /** @var string */
    protected $action;
    /** @var array Form buttons */
    protected $buttons   = array();
    /** @var array Data cache from request */
    protected $data      = array();
    /** @var Request */
    protected $request = null;

    /** @var bool enable HTML5 validate */
    protected $htmlValidate;

    protected $errorRequired  = 0;
    protected $errorUnType    = 0;

    protected $feedback = array();


    /**
     * Создает форму согласно конфигу
     * @param $options
     *
     * @throws Exception
     */
    public function __construct($options)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
                'name'
            ));
        $resolver->setDefaults(array(
                'type'      => 'form',
                'action'    => '',
                'class'     => '',
                'debug'     => false,
                'fields'    => array(),
                'id'        => '',
                'validate'  => false,
                'method'    => 'post',
                'enctype'   => null,
            ));
        $resolver->setAllowedValues('enctype', array(null, 'application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'));
        $options = $this->options = $resolver->resolve($options);

        $this->name   = $options['name'];
        $this->method = $options['method'];
        $this->action = $options['action'];
        $this->class  = $options['class'];// : 'form-horizontal';
        $this->debug  = $options['debug'];
        $this->id     = $options['id'];
        $this->type   = $options['type'];
        $this->htmlValidate = $options['validate'];

        foreach ($options['fields'] as $fieldName => $fieldOptions) {
            $fieldOptions['name'] = $fieldName;
            $fieldObject = $this->createField($fieldOptions);

            if (isset($fieldOptions['type']) && in_array($fieldOptions['type'], array('submit', 'reset', 'button'))) {
                $this->addButton($fieldObject);
            } else {
                $this->setChild($fieldObject);
            }
        }
    }

    public function createView(SymfonyFormView $parentView = null)
    {
        $view = new FormView($parentView);
        $view->vars = array(
            'value' => null,
            'type' => $this->getType(),
            'attr' => array(
                'id' => $this->getId(),
                'name' => $this->getName(),
                'action' => $this->getAction(),
                'method' => $this->getMethod(),
                'class' => $this->getClass(),
                'errors' => $this->getErrors(),
                'enctype' => $this->options['enctype'],
                'validate' => $this->htmlValidate,
            ),
        );

        if ($this->children) {
            /** @var FormTypeAbstract $child */
            foreach ($this->children as $child) {
                $view->children[$child->getName()] = $child->createView($view);
            }
        }

        if ($this->buttons) {
            /** @var FormTypeAbstract $child */
            foreach ($this->buttons as $child) {
                $view->children[$child->getName()] = $child->createView($view);
            }
        }

        return $view;
    }

    /**
     * @param bool $hint
     * @param bool $buttons
     * @deprecated
     * @return mixed
     */
    public function html($hint = true, $buttons = true)
    {
        return $this->createView()->html(array('hint'=>$hint, 'buttons'=>$buttons));
    }

    /**
     * @param FormFieldAbstract $field
     */
    public function addButton(FormFieldAbstract $field) {
        $this->buttons[$field->getId()] = $field;
    }

    /**
     * Create new field
     * @param $options
     *
     * @return FormFieldAbstract
     */
    public function createField($options)
    {
        $options['type'] = isset($options['type']) ? $options['type'] : 'text';

        // тип hidden преобразовать в скрытый text
        if ($options['type'] == 'hidden') {
            $options['type']   = 'text';
            $options['hidden'] = true;
        }

        // физический класс обработки поля
        $fieldClass = '\\Sfcms\\Form\\Field\\' . ucfirst(strtolower($options['type']));

        /** @var FormFieldAbstract $fieldObject */
        $fieldObject = new $fieldClass($options);
        $fieldObject->setParent($this);

        if (!empty($options['hidden'])) {
            $fieldObject->hide();
        }

        return $fieldObject;
    }

    /**
     * @param Request $request
     * @deprecated
     * @return bool
     */
    public function getPost(Request $request)
    {
        return $this->handleRequest($request);
    }

    /**
     * Дернет из запроса значения полей
     * @param Request $request
     *
     * @return bool
     */
    public function handleRequest(Request $request)
    {
        $this->request = $request;
        if ($this->isSent($request)) {
            $data = $this->data;
            $files = $request->files->get($this->getName());
            /** @var $child FormFieldAbstract */
            foreach ($this->children as $child) {
                if (is_object($child)) {
                    if (isset($data[$child->getName()])) {
                        $child->setValue($data[$child->getName()]);
                    }
                    if ($child instanceof File && isset($files[$child->getName()])) {
                        $child->setValue($files[$child->getName()]);
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Отправлена ли форма?
     * @param Request $request
     *
     * @return bool
     */
    public function isSent(Request $request)
    {
        $data = $this->getMethod() == 'post' ? $request->request : $request->query;
        if ($data->has($this->name)) {
            $this->data = $data->get($this->name);
            return true;
        }

        return false;
    }

    /**
     * Установит или вернет значение имени формы
     * @param string $name
     * @return string|void
     */
    public function name($name = '')
    {
        if ($name) {
            $this->name = $name;
        } else {
            return $this->name;
        }
        return $this;
    }

    /**
     * Вернет массив значений
     * Как правило, нужно для использования с базой данных
     * @param $toString
     * @return array
     */
    public function getData($toString = false)
    {
        $data = array();
        foreach ($this->children as $child) {
            /** @var $child FormTypeAbstract */
            if (is_object($child)) {
                if ($this->isDatable()) {
                    $data[$child->getName()] = $child->getData($toString);
                }
            }
        }
        return $data;
    }

    /**
     * Settings array of values
     * Как правило, нужно для использования с базой данных
     *
     * @param array|\ArrayAccess $data
     * @return bool
     * @throws Exception
     */
    public function setData($data)
    {
        if (count($this->children) == 0) {
            throw new Exception('Form has not children');
        }

        if ($data) {
            foreach ($this->children as $child) {
                /** @var $child FormFieldAbstract */
                if (is_object($child) && $child->isDatable()) {
                    if (isset($data[$child->getName()])) {
                        $child->setValue($data[$child->getName()]);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check for valid form values
     *
     * @return bool
     */
    public function validate()
    {
        if (null === $this->request) {
            throw new \RuntimeException('Validation requires an Request');
        }
        $valid = true;
        foreach ($this->children as $child) {
            if (is_object($child)) {
                /** @var $child FormFieldAbstract */
                $ret = $child->validate($this->request);
                $valid &= ($ret == 1) ? true : false;
            }
        }

        return $valid;
    }


    /**
     * Добавит сообщение обратной связи
     * @param  $msg
     * @return void
     */
    public function addFeedback( $msg )
    {
        array_push($this->feedback, $msg);
    }

    /**
     * Вернет сообщения обратной связи как массив
     * @return array
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Вернет сообщения обратной связи как строку
     * @param string $sep
     * @return string
     */
    public function getFeedbackString($sep = '<br>')
    {
        return join($sep, $this->feedback);
    }


    /**
     * Изменит тип поля
     * @param $type
     */
    public function changeFieldType($type)
    {

    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return strtolower($this->method);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * The offset to assign the value to.
     * @param mixed $value
     * The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getChild($offset)->setValue($value);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        try {
            return $this->getChild($offset)->getValue();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * An offset to check for.
     * @return boolean Returns true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->getChild($offset) ? true : false;
    }
}
