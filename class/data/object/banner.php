<?php
/**
 * Объект баннера
 * @author Voronin Vladimir (voronin@stdel.ru)
 *
 *
 * @property $id
 * @property $cat_id
 * @property $name
 * @property $url
 * @property $path
 * @property $count_show
 * @property $count_click
 * @property $target
 * @property $content
 */
class Data_Object_Banner extends Data_Object
{
    /**
     * Вернет адрес перехода для баннера
     * @return string
     */
    public function getUrl()
    {
        if ( preg_match('/^http/', $this->data['url']) ) {
            $url = $this->data['url'];
        } else {
            $url =  'http' . ( isset( $_SERVER[ 'HTTPS' ] ) && 'off' !== $_SERVER['HTTPS'] ? "s" : "" ) . '://'
                           . $_SERVER[ "HTTP_HOST" ] . $this->data['url'];
        }
        return $url;
    }

    public function getBlock()
    {
        if ( ! $this->id ) {
            throw new Data_Exception('Identifier for banner not found. Banner does not exist.');
        }
        return Sfcms::html()->link( $this->content, 'banner/redirectbanner', array('htmlTarget'=>$this->target,'id'=>$this->id) );
    }
}
