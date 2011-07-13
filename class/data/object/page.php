<?php
/**
 * Объект Страницы
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Data_Object_Page extends Data_Object
{
    /**
     * @return string
     */
    public function getAlias()
    {
        if ( ! $this->getId() ) {
            return null;
        }
        return $this->data['alias'];
    }
}
