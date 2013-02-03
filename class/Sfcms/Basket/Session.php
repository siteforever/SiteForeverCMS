<?php

use Sfcms\Basket\Base as Basket;

/**
 * @author Ermin Nikolay
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Sfcms_Basket_Session extends Basket
{
    public function load()
    {
        if ( App::getInstance()->getSession()->get('basket') ) {
            $this->data = App::getInstance()->getSession()->get('basket');
        }
        else {
            $this->data = array();
            $this->save();
        }
    }
    
    public function save()
    {
        App::getInstance()->getSession()->set('basket', $this->data);
    }
}