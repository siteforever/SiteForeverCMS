<?php
/**
 * Cataloge gallery object
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 *
 * @property $id
 * @property $cat_id
 * @property $image
 * @property $thumb
 * @property $hidden
 * @property $main
 */
class Data_Object_CatalogGallery extends Data_Object
{
    /**
     * @return mixed
     */
    public function getThumb()
    {
        if ( $this->data['thumb'] ) {
            return $this->data['thumb'];
        }
        return $this->image;
}
}
