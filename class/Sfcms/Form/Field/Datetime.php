<?php
/**
 * DateTime forms field
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Form\Field;

use Sfcms\Form\Field;

class Datetime extends Field
{
    public function setValue($value)
    {
        if (is_string($value)) {
            $value = new \DateTime($value);
        }
        $this->_value = $value->getTimestamp();
        return $this;
    }

    public function getValue()
    {
        $result = new \DateTime();
        $result->setTimestamp($this->_value);
        return $result;
    }

    public function getStringValue()
    {
        $value = new \DateTime();
        if ($this->_value) {
            $value->setTimestamp($this->_value);
        }
        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        $this->_class = 'input-large';
        if (!$this->_readonly) {
            $field['class'] = array('input-append', 'datetime');
        }
        return parent::htmlInput( $field )
            . '<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>';
    }

    protected function checkValue($value)
    {
        return true;
    }


}
