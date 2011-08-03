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

    /**
     * @return string
     */
    public function createUrl()
    {
        if ( ! $this->get('link') ) {
            return App::getInstance()->getRouter()->createServiceLink('page','index',array('id'=>$this->getId()));
        }
        return App::getInstance()->getRouter()->createServiceLink(
            $this->get('controller'),
            $this->get('action'),
            array('id'=>$this->get('link'))
        );
    }
}
