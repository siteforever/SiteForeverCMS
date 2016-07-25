<?php
namespace Sfcms\Form;

use Sfcms\Request;
use Symfony\Component\Form\FormView as SymfonyFormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Интерфейс классов полей формы
 * @author keltanas <keltanas@gmail.com>
 *
 * Для полей формы рекомендуется переопределять следующие методы xxx
 */
abstract class FormFieldAbstract extends FormTypeAbstract
{
    const FILTER_EMAIL = '/^[\.\-_A-Za-z0-9]{2,}?@[\.\-A-Za-z0-9]{2,}?\.[A-Za-z0-9]{2,6}$/';
    const FILTER_URL = '/^http[s]?:\/\/[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/';
    const FILTER_PHONE = '/(\+?\d?)[- ]?\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{2,4})[- ]?(\d{2,4})$/';
    const FILTER_DEFAULT = '/.*/';

    /** @var Request */
    protected $request = null;

    protected $label;
    protected $notice;

    /** @var string RegExp for filtering values */
    protected $filter = self::FILTER_DEFAULT;

    protected $readonly = false;
    protected $required = false;
    protected $disabled = false;
    protected $hidden   = false;

    /**
     * Есть ли у поля ошибка
     * @var boolean
     */
    protected   $error = false;
    /**
     * Текст ошибки поля
     * @var string
     */
    protected   $msg;

    /**
     * Создаем поле формы
     * @param array $options
     */
    public function __construct($options)
    {
        foreach ($options as $i => $p) {
            if (is_int($i)) {
                switch (strtolower(trim($p))) {
                    case 'readonly':
                        $options['readonly'] = true;
                        break;
                    case 'disable':
                        $options['disabled'] = true;
                        break;
                    case 'hidden':
                        $options['hidden'] = true;
                        break;
                    case 'required':
                        $options['required'] = true;
                        break;
                }
                unset($options[$i]);
            }
        }

        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
                'name'
            ));
        $resolver->setDefaults($this->getDefaultOptions());
        $options = $this->options = $resolver->resolve($options);

        $this->name = $options['name'];
        $this->type = $options['type'];
        $this->class = $options['class'];
        $this->label = $options['label'];
        $this->notice = $options['notice'];

        $this->disabled = $options['disable'];
        $this->readonly = $options['readonly'];
        $this->required = $options['required'];
        $this->hidden   = $options['hidden'];

        switch($options['filter']) {
            case 'email':
                $this->filter = self::FILTER_EMAIL;
                break;
            case 'url':
                $this->filter = self::FILTER_URL;
                break;
            case 'phone':
                $this->filter = self::FILTER_PHONE;
                break;
            default:
                $this->filter = $options['filter'];
        }

        if (!empty($options['value'])) {
            $this->setValue(trim($options['value']));
        }

        $this->options   = $options;
    }

    protected function getDefaultOptions()
    {
        return array(
            'type'      => $this->getType(),
            'class'     => $this->getClass(),
            'id'        => $this->getId(),
            'label'     => $this->getLabel(),
            'notice'    => $this->notice,
            'value'     => '',
            'hidden'    => $this->hidden,
            'disable'   => $this->disabled,
            'readonly'  => $this->readonly,
            'required'  => $this->required,
            'variants'  => array(),
            'multiple'  => false,
            'placeholder' => '',
            'autocomplete'  => true,
            'filter'    => $this->filter,
        );
    }

    public function createView(SymfonyFormView $parentView = null)
    {
        if (null == $parentView) {
            if ($this->getParent()) {
                // if called directly
                $view = $this->getParent()->createView();
                return $view[$this->getName()];
            }
        }
        $view = new FormView($parentView);
        $view->vars = array(
            'type' => $this->getType(),
            'attr' => array(
                'id' => $this->getId(),
                'name' => $this->getName(),
                'type' => $this->getType(),
                'label' => $this->getLabel(),
                'notice' => $this->notice,
                'class' => $this->getClass(),
                'errors' => $this->getErrors(),
                'error'  => $this->error,
                'msg'    => $this->msg,
                'value'  => $this->getStringValue(),
                'required' => $this->isRequired(),
                'hidden'   => $this->getHidden(),
                'readonly' => $this->getReadonly(),
                'disabled' => $this->disabled,
                'autocomplete' => isset($this->options['autocomplete']) && !$this->options['autocomplete'] ? false : true,
                'variants' => $this->getVariants(),
                'multiple' => empty($this->options['multiple']) ? false : true,
                'placeholder' => $this->options['placeholder'],
            ),
        );

        return $view;
    }

    /**
     * Строгий поиск в массиве
     * @param mixed $val
     * @param array $array
     * @return boolean
     */
    protected function in_array_strict( $val, &$array )
    {
        foreach( $array as $arr ) {
            if ( $arr === $val ) {
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * Скрыть поле
     */
    public function hide()
    {
        $this->hidden   = true;
    }

    /**
     * Показать поле
     */
    public function show()
    {
        $this->hidden   = false;
    }

    /**
     * Назначать новую метку
     * @param $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     *
     * @return boolean
     */
    protected function checkValue($value)
    {
        if (!$this->isEmpty() && trim($this->filter)) {
            return preg_match($this->filter, trim($value));
        }

        return true;
    }

    /**
     * Проверка, является ли поле обязательным для заполнения
     * @return boolean
     */
    public function isRequired()
    {
        if (!$this->hidden) {
            return $this->required;
        }

        return false;
    }

    /**
     * Устанавливает поле, как требуемое
     * @param boolean $required
     * @return void
     */
    public function setRequired($required = true)
    {
        $this->required = $required;
    }

    /**
     * Проверка, является ли значение поля "пустым"
     * @return boolean
     */
    public function isEmpty()
    {
        $value = trim($this->value);
        if (isset($this->options['empty'])) {
            if ($this->options['empty'] == $this->value) {
                return true;
            }
        } else {
            if (empty($value)) {
                return true;
            }
            if (in_array($this->getType(), array('text', 'textarea')) && $value == '') {
                return true;
            }
            if (in_array($this->getType(), array('int', 'float')) && $value == '0') {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверит значение поля на соответствие типу, а также заполнено ли
     * значение обязательного поля
     *
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request)
    {
        $this->request = $request;

        $classes = explode(' ', trim($this->class));
        foreach ($classes as $i => $class) {
            if ($class == 'error') {
                unset($classes[$i]);
            }
        }

        // по умолчанию валидно
        $this->error   = 0;

        $this->checkValid($request);

        if ($this->parent && $this->error > 0) {
            $this->parent->addError($this->name, $this->msg);
            $classes[] = 'error';
            $this->class = join(' ', $classes);
        }

        return !$this->error;
    }

    /**
     * Проверка валидности
     *
     * @param Request $request
     * @return boolean
     */
    protected function checkValid(Request $request)
    {
        if ($this->isRequired() && $this->isEmpty()) {
            //    или если его значение пустое
            $this->error = 2;
            $this->msg   = '"%label%" is required';

            return false;
        }

        if (!$this->isRequired() && $this->isEmpty()) {
            return true;
        }

        if (!$this->checkValue($this->getValue())) {
            $this->error = 3;
            $this->msg   = $this->msg ? : '"%label%" not corresponded specified format';

            return false;
        }

        return true;
    }

    /**
     * @param $readonly
     */
    public function setReadonly( $readonly )
    {
        $this->readonly = $readonly;
    }

    /**
     * @return boolean
     */
    public function getReadonly()
    {
        return $this->readonly;
    }

    /**
     * @return boolean
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Установить варианты выбора (для select и radio)
     * @param $list
     */
    public function setVariants($list)
    {
        $this->options['variants'] = $list;
    }

    /**
     * Добавить варианты выбора к уже имеющимся (для select и radio)
     * @param $list
     */
    public function addVariants($list)
    {
        $this->options['variants'] = array_merge($this->options['variants'], $list);
    }

    public function getVariants()
    {
        return isset($this->options['variants']) ? $this->options['variants'] : array();
    }
}
