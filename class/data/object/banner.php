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

    public function getUrl()
    {
        if ( preg_match('/^http/', $this->data['url']) ) {
            $url = $this->data['url'];
        } else {
            $url = ( isset( $_SERVER[ 'SSL' ] ) ? "https://" : "http://" ) . $_SERVER[ "HTTP_HOST" ] . $this->data['url'];
        }
        return $url;
    }
}
