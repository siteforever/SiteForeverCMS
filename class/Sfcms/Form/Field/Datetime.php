<?php
/**
 * DateTime forms field
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Datetime extends FormFieldAbstract
{
    protected $type = 'datetime';

    protected $class = 'input-append datetime';

    public function setValue($value)
    {
        if (is_string($value)) {
            $value = new \DateTime($value);
        }
        $this->value = $value->getTimestamp();
        return $this;
    }

    public function getValue()
    {
        $result = new \DateTime();
        $result->setTimestamp($this->value);
        return $result;
    }

    public function getStringValue()
    {
        $value = new \DateTime();
        if ($this->value) {
            $value->setTimestamp($this->value);
        }
        return $value->format('Y-m-d H:i:s');
    }

//    /**
//     * Вернет HTML для поля
//     * @var array $filed
//     * @return string
//     */
//    public function htmlInput( $field )
//    {
//        $this->class = 'input-large';
//        if (!$this->readonly) {
//            $field['class'] = array('input-append', 'datetime');
//        }
//        return parent::htmlInput( $field )
//            . '<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>';
//    }

    protected function checkValue($value)
    {
        return true;
    }


}
