<?php
/**
 * Модель Алиаса
 * @author keltanas <nikolay@gmail.com>
 * @link http://siteforever.ru
 */

class Model_Alias extends Model
{
    public function findByAlias( $path )
    {
        $result  = $this->find(
            array(
                'cond'      => ' `alias` = ? ',
                'params'    => array( $path ),
            )
        );
        return $result;
    }

}
