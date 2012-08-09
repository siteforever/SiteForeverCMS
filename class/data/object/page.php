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
    public function getAlias()
    {
        if ( ! $this->data['alias'] ) {
            $this->data['alias'] = trim( Sfcms_i18n::getInstance()->translit(strtolower($this->data['name'])), '/ ' );
        }
        return $this->data['alias'];
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->alias;
    }

    /**
     * Вернет ссылку на редактирования модуля в админке
     * @return null|string
     */
    public function getLinkEdit()
    {
        $link = null;
        $linkUrl = null;
        switch ( $this->controller ) {
            case 'catalog':
                $linkUrl = Siteforever::html()->url('catalog/category', array('edit'=>$this->link));
                break;
            case 'gallery':
                $linkUrl = Siteforever::html()->url('gallery/editcat', array('id'=>$this->link));
                break;
            case 'news':
                $linkUrl = Siteforever::html()->url('news/catedit', array('id'=>$this->link));
                break;
        }
        if ( $linkUrl ) {
            $link = Siteforever::html()->link( icon( 'link', t('Go to the module') ), $linkUrl );
        }
//        $link = "<a href='{$linkUrl}'>" . icon( 'link', 'Перейти к модулю' ) . '</a>';
        return $link;
    }


    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        if ( $this->data['title'] ) {
            return $this->data['title'];
        }
        return $this->data['name'];
    }


    /**
     * Создаст serialize путь для конвертации в breadcrumbs
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
            $obj = $this->getModel()->find( $obj->get( 'parent' ) );
        }
        $path   = array_reverse($path);
        return serialize($path);
    }
}
