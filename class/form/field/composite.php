<?php
/**
 * Составное поле
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
 
abstract class Form_Field_Composite extends Form_Field
{
    /**
     * Добавить варианты выбора к уже имеющимся (для select и radio)
     * @param $list
     */
    public function addVariants( $list )
    {
        $this->_params['variants'] = array_merge( $this->_params['variants'], $list );
    }


}
