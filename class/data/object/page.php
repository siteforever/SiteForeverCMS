<?php
/**
 * Объект Страницы
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 *
 * @property int $id
 * @property int $parent
 * @property string $name
 * @property string $template
 * @property string $alias
 * @property int $alias_id
 * @property string $path
 */

class Data_Object_Page extends Data_Base_Page
{
    /**
     * @return string
     */
    public function getAlias()
    {
//        if ( ! $this->getId() ) {
//            return null;
//        }
        $result = '';
        if ( $this->data['alias'] ) {
            $result = $this->data['alias'];
        } else {
            $result = Sfcms_i18n::getInstance()->translit($this->data['name']);
            $this->data['alias']    = $result;
        }

        return $result;
    }

    /**
     * Вернет выделенный контент
     * @param array $words
     * @return array|Data_Object|mixed|null
     */
    public function getHlContent( array $words )
    {
        $result = $this->get('content');
        foreach ( $words as $word ) {
            if ( strlen( $word ) > 3 ) {
                $result = str_ireplace( $word, '<b class="highlight">'.$word.'</b>', $result );
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function createUrl()
    {
        if ( 'page' == $this->get('controller') ) {
            return App::getInstance()->getRouter()->createServiceLink('page','index',array('id'=>$this->getId()));
        }
        return App::getInstance()->getRouter()->createServiceLink(
            $this->get('controller'),
            $this->get('action'),
            array('id'=>$this->get('link'))
        );
    }

    /**
     * Создаст json путь для конвертации в breadcrumbs
     * @return string
     */
    public function createPath()
    {
        $path   = array();
        $obj    = $this;
        while ( null !== $obj ) {
            $path[] = array(
                'id'    => $obj->getId(),
                'name'  => $obj->get('name'),
                'url'   => $obj->getAlias(),
            );
            $obj    = $this->getModel()->find( $obj->get('parent') );
        }
        $path   = array_reverse($path);
        return json_encode($path);
    }
}
