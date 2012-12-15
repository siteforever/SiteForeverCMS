<?php
/**
 * Модель комментариев
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
namespace Module\System\Model;

use Sfcms_Model;

class CommentsModel extends Sfcms_Model
{

    public function tableClass()
    {
        return 'Data_Table_Comments';
    }

    public function objectClass()
    {
        return 'Data_Object_Comments';
    }

}
