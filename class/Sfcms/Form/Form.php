<?php
namespace Sfcms\Form;

use Sfcms\Form\Render;
use Sfcms\i18n;

/**
 * Класс формы
 * @author keltanas
 */
class Form extends Render implements \ArrayAccess
{

    /**
     * @return i18n
     */
    public function i18n()
    {
        return \App::getInstance()->getContainer()->get('i18n');
    }

    /**
     * Напечатать переведенный текст
     * @param string $cat
     * @param string $text
     * @param array $params
     * @return mixed
     */
    public function t($cat, $text = '', $params = array())
    {
        return call_user_func_array(array($this->i18n(),'write'), func_get_args());
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
        // TODO: Implement offsetUnset() method.
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
        $this->getField( $offset )->setValue( $value );
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
            return $this->getField( $offset )->getValue();
        } catch ( Exception $e ) {
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
        return $this->getField( $offset ) ? true : false;
    }
}
